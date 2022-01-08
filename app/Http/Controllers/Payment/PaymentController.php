<?php

namespace App\Http\Controllers\Payment;

use App\Models;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use General;
use App\Services\Midtrans; // => letakkan pada bagian atas class

class PaymentController extends Controller
{

    /**
	 * Receive notification from payment gateway
	 *
	 * @param Request $request payment data
	 *
	 * @return json
	 */
	public function notification(Request $request)
	{
		$payload = $request->getContent();
		$notification = json_decode($payload);

		$validSignatureKey = hash("sha512", $notification->order_id . $notification->status_code . $notification->gross_amount . env('MIDTRANS_SERVER_KEY'));

		if ($notification->signature_key != $validSignatureKey) {
			//return response(['message' => 'Invalid signature'], 403);
		}

		//$this->initPaymentGateway();
		$statusCode = null;

        // Set your Merchant Server Key
        \Midtrans\Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        // Set to Development/Sandbox Environment (default). Set to true for Production Environment (accept real transaction).
        \Midtrans\Config::$isProduction = true;
        // Set sanitization on (default)
        \Midtrans\Config::$isSanitized = true;
        // Set 3DS transaction for credit card to true
        \Midtrans\Config::$is3ds = true;

		$paymentNotification = new \Midtrans\Notification();
		$subs = Models\Subs::where('uuid', $paymentNotification->order_id)->firstOrFail();

		if ($subs->subs_status == 'PAID') {
			return response()->json([
				'message' => 'Failed',
				'info' => 'Langganan Telah Dibayar Sebelumnya',
				//'data' => $result
			]);
		}

		$transaction = $paymentNotification->transaction_status;
		$type = $paymentNotification->payment_type;
		$orderId = $paymentNotification->order_id;
		$fraud = $paymentNotification->fraud_status;

		$vaNumber = null;
		$vendorName = null;
		if (!empty($paymentNotification->va_numbers[0])) {
			$vaNumber = $paymentNotification->va_numbers[0]->va_number;
			$vendorName = $paymentNotification->va_numbers[0]->bank;
		}

		$paymentStatus = null;
		if ($transaction == 'capture') {
			// For credit card transaction, we need to check whether transaction is challenge by FDS or not
			if ($type == 'credit_card') {
				if ($fraud == 'challenge') {
					// TODO set payment status in merchant's database to 'Challenge by FDS'
					// TODO merchant should decide whether this transaction is authorized or not in MAP
					$paymentStatus = Models\Payment::CHALLENGE;
				} else {
					// TODO set payment status in merchant's database to 'Success'
					$paymentStatus = Models\Payment::SUCCESS;
				}
			}
		} else if ($transaction == 'settlement') {
			// TODO set payment status in merchant's database to 'Settlement'
			$paymentStatus = Models\Payment::SETTLEMENT;
		} else if ($transaction == 'pending') {
			// TODO set payment status in merchant's database to 'Pending'
			$paymentStatus = Models\Payment::PENDING;
		} else if ($transaction == 'deny') {
			// TODO set payment status in merchant's database to 'Denied'
			$paymentStatus = Models\Payment::DENY;
		} else if ($transaction == 'expire') {
			// TODO set payment status in merchant's database to 'expire'
			$paymentStatus = Models\Payment::EXPIRE;
		} else if ($transaction == 'cancel') {
			// TODO set payment status in merchant's database to 'Denied'
			$paymentStatus = Models\Payment::CANCEL;
		}

		$payment_id = 'PAY_'.
		date('Y').'_'.
		$this->numberToRomanRepresentation(date('m')).'_'.
		$this->numberToRomanRepresentation(date('d')).'_'.
		substr($subs->user->uuid, 0, 8).'_'.
		date('His');

		$paymentParams = [
			'id_subs' => $subs->id,
			'tgl_pembayaran' => date("Y/m/d H:i:s"),
			'transaction_id' => $paymentNotification->transaction_id,
			'method' => 'midtrans',
			'status' => $paymentStatus,
			'amount' => $paymentNotification->gross_amount,
			'token' => $paymentNotification->transaction_id,
			'payloads' => $payload,
			'payment_type' => $paymentNotification->payment_type,
			//'va_number' => $vaNumber,
			//'vendor_name' => $vendorName,
			//'biller_code' => $paymentNotification->biller_code,
			//'bill_key' => $paymentNotification->bill_key,
			'uuid' => $payment_id,
		];

		$payment = Models\Payment::create($paymentParams);
		// return $user = Models\User::where('id',$subs->id_user)->update([
		// 	'tgl_langganan_akhir' => (new \DateTime(date('Y-m-d')))->modify('+'.(30*$subs->packet->lama_paket).' day')->format('Y-m-d'),
		// ]);
			$subs1 = Models\Subs::where('id',$subs->id)->update([
				'subs_status' => strtoupper($paymentStatus)
			]);

		if (in_array($payment->status, [Models\Payment::SUCCESS, Models\Payment::SETTLEMENT])) {
			$subs1 = Models\Subs::where('id',$subs->id)->update([
				'subs_status' => 'PAID'
			]);
			$user = Models\User::where('id',$subs->id_user)->update([
				'tgl_langganan_akhir' => (new \DateTime(date('Y-m-d')))->modify('+'.(30*$subs->packet->lama_paket).' day')->format('Y-m-d'),
			]);
		}	
			
		return response()->json([
			'message' => 'Success',
			'info' => 'Status Pembayaran ['.$paymentStatus.']',
			//'data' => $result
		]);
	}

	private function numberToRomanRepresentation($number) {
        $map = array('M' => 1000, 'CM' => 900, 'D' => 500, 'CD' => 400, 'C' => 100, 'XC' => 90, 'L' => 50, 'XL' => 40, 'X' => 10, 'IX' => 9, 'V' => 5, 'IV' => 4, 'I' => 1);
        $returnValue = '';
        while ($number > 0) {
            foreach ($map as $roman => $int) {
                if($number >= $int) {
                    $number -= $int;
                    $returnValue .= $roman;
                    break;
                }
            }
        }
        return $returnValue;
    }

	/**
	 * Show completed payment status
	 *
	 * @param Request $request payment data
	 *
	 * @return void
	 */
	public function completed(Request $request)
	{
		$code = $request->query('order_id');
		$paymentStatus = $request->transaction_status;
		$subs = Models\Subs::where('uuid', $code)->firstOrFail();
		
		if ($subs->subs_status != 'PAID') {
			return redirect('failed?order_id='. $code);
		}
			
		return response()->json([
			'message' => 'Success',
			'info' => 'Status Pembayaran Berhasil',
			//'data' => $result
		]);
	}

	/**
	 * Show unfinish payment page
	 *
	 * @param Request $request payment data
	 *
	 * @return void
	 */
	public function unfinish(Request $request)
	{
		$code = $request->query('order_id');
		$subs = Models\Subs::where('uuid', $code)->firstOrFail();

		\Session::flash('error', "Sorry, we couldn't process your payment.");

		//return redirect('orders/received/'. $order->id);
		return response()->json([
			'message' => 'Failed',
			'info' => 'Pembayaran Tidak Dapat Diproses',
			//'data' => $result
		]);
	}

	/**
	 * Show failed payment page
	 *
	 * @param Request $request payment data
	 *
	 * @return void
	 */
	public function failed(Request $request)
	{
		$code = $request->query('order_id');
		$subs = Models\Subs::where('uuid', $code)->firstOrFail();

		\Session::flash('error', "Sorry, we couldn't process your payment.");

		//return redirect('orders/received/'. $order->id);
		return response()->json([
			'message' => 'Failed',
			'info' => 'Pembayaran Tidak Dapat Diproses',
			//'data' => $result
		]);
	}
}
