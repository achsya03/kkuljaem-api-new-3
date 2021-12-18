<?php

namespace App\Http\Controllers\Payment;

use App\Models;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Helper;
use App\Http\Requests\OrderRequest;

class SubsController extends Controller
{
	private function statUser($user){
        $stUsr = "Non-Member";
        $jenis_akun=['No Sign','Helm','Crown Silver'];
        if(date_format(date_create($user->tgl_langganan_akhir),"Y/m/d") >= date('Y/m/d')){
            $stUsr = "Member";
        }
        
        if($user->jenis_pengguna!='0'){
            if(count($user->detailMentor)>0){
                if($user->detailMentor[0]->url_foto!=null || $user->detailMentor[0]->url_foto!=''){$data['foto'] = $user->detailMentor[0]->url_foto;}
            }
        }
        $data['tgl_akhir_langganan'] = $user->tgl_langganan_akhir;
        $data['nama'] = $user->nama;
        $data['status_member'] = $stUsr;
        $data['jenis_akun'] = $jenis_akun[$user->jenis_akun];

        return $data;
    }

	public function subsReport(Request $request){
        $subs = Models\Subs::orderBy('tgl_subs','DESC')->get();

		$res = [];
		$result = [];
		$arr = [];
		$total = 0;
		for($i=0;$i<count($subs);$i++){
			$pay_type = '-';
			if(count($subs[$i]->payment)>1){
				$pay_type = $subs[$i]->payment[count($subs[$i]->payment)-1]->payment_type;
			}elseif(count($subs[$i]->payment)>0){
				$pay_type = $subs[$i]->payment[0]->payment_type;
			}
			$status = 'TUNGGU';
			if($subs[$i]->subs_status=='PAID'){
				$status = 'BERHASIL';
			}if($subs[$i]->subs_status=='UNPAID' && date_format(date_create($subs[$i]->tgl_akhir_bayar),"Y/m/d H:i:s") < date('Y/m/d H:i:s')){
				$status = 'GAGAL';
			}
			$res = [
				'tipe_transaksi'=> $pay_type,
				'jenis'=>'Masuk',
				'tgl_subs'=>$subs[$i]->tgl_subs,
				'tgl_akhir_bayar'=>$subs[$i]->tgl_akhir_bayar,
				'id_permintaan'=>$subs[$i]->uuid,
				'email'=>$subs[$i]->user->email,
				'status'=>$status,
			];
			$arr[$i] = $res;
			if($status == 'BERHASIL'){
				$total += $subs[$i]->harga;
			}
		}

		$result = [
			'total_saldo'=>$total,
			'jml_transaksi'=>count($subs),
			'subs'=>$arr
		];

        return response()->json([
			'message' => 'Success',
			'data' => $result
		]);
    }

	public function subsReportDetail(Request $request){

		if(!$uuid = $request->token){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Token tidak sesuai'
            ]);
        }

        if(count($subs = Models\Subs::where('uuid',$uuid)->get())==0){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Token tidak sesuai'
            ]);
		}

		$res = [];
		$result = [];
		$arr = [];
		$total = 0;
		for($i=0;$i<count($subs);$i++){
			$pay_type = '';
			if(count($subs[$i]->payment)>1){
				$pay_type = $subs[$i]->payment[count($subs[$i]->payment)-1]->payment_type;
			}elseif(count($subs[$i]->payment)>0){
				$pay_type = $subs[$i]->payment[0]->payment_type;
			}
			$status = 'TUNGGU';
			if($subs[$i]->subs_status=='PAID'){
				$status = 'BERHASIL';
			}if($subs[$i]->subs_status=='UNPAID' && date_format(date_create($subs[$i]->tgl_akhir_bayar),"Y/m/d H:i:s") < date('Y/m/d H:i:s')){
				$status = 'GAGAL';
			}
			$res = [
				'tipe_transaksi'=> $pay_type,
				'jenis'=>'Masuk',
				'tgl_subs'=>$subs[$i]->tgl_subs,
				'tgl_akhir_bayar'=>(new \DateTime($subs[$i]->tgl_subs))->modify('+'.(30*$subs[$i]->packet->lama_paket).' day')->format('Y-m-d H:i:s'),
				'id_permintaan'=>$subs[$i]->uuid,
				'jumlah'=>$subs[$i]->harga,
				'status'=>$status,
			];
			$arr[$i] = $res;
			$total += $subs[$i]->harga;
		}
		$student_det = Models\DetailStudent::where('uuid',$subs[0]->user->uuid)->first();
		$alamat = '-';
		if($student_det != null){
			$alamat = $student_det->alamat;
		}
		$res1 = [
			'packet_name'=> 'PAKET '.$subs[0]->packet->lama_paket.' bulan',
			'nama_user'=>$subs[0]->user->nama,
			'email'=>$subs[0]->user->email,
			'alamat'=>$alamat,
		];

		$result = [
			'subs'=>$arr[0],
			'detail_sub'=>$res1
		];

        return response()->json([
			'message' => 'Success',
			'data' => $result
		]);
    }


    public function __construct(Request $request){
        $this->middleware('auth');
    }

	
	/**
	 * Checkout process and saving order data
	 *
	 * @param OrderRequest $request order data
	 *
	 * @return void
	 */
	public function doCheckout(Request $request)
	{
		if(!$uuid = $request->token){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Token tidak sesuai'
            ]);
        }
		$user = $request->user();
		if(date_format(date_create($user->tgl_langganan_akhir),"Y/m/d") >= date('Y/m/d')){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Anda Masih Memiliki Langganan Aktif'
            ]);
		}
		if($user->jenis_akun == '0'){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Silahkan Lengkapi Data Anda Dahulu'
            ]);
		}
		if(count($user->detailStudent) == '0'){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Silahkan Lengkapi Data Anda Dahulu'
            ]);
		}
		$ref = [
			'kode'=>'',
			'nama'=>''
		];

		if(isset($request->referal)){
        	$ref=Models\Reference::where('kode',$request->referal)->get();
			if(count($ref)==0){
				return response()->json([
					'message' => 'Failed',
					'error' => 'Kode Referal Tidak Terdaftar'
				]);
			}
		}
        
        $packet = Models\Packet::where('uuid',$uuid)->get();
        if(count($packet) == 0){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Token tidak sesuai'
            ]);
        }
		$act_subs = Models\Subs::where('id_packet',$packet[0]->id)
					->where('id_user',$user->id)
					->orderBy('tgl_subs','DESC')->get();
		for($i=0;$i<count($act_subs);$i++){
			if(date_format(date_create($act_subs[$i]->tgl_akhir_bayar),"Y/m/d H:i:s") > date('Y/m/d H:i:s') && ($act_subs[$i]->subs_status == "UNPAID")){
				//return $act_subs[$i];
				$packet = Models\Packet::select([
					'lama_paket',
					'harga',
					'status_aktif',
					'uuid',
					])->where('id',$act_subs[$i]->id_packet)->first();
				//unset($class_cat['id']);
				$refId = '';
				$kode = '';
				$nama = '';
		
				if($act_subs[$i]->id_reference!=null || $act_subs[$i]->id_reference!= ''){
					$ref=Models\Reference::where('id',$act_subs[$i]->id_reference)->first();
					if($ref!=null || $ref != ''){
						$refId = $ref->id;
						$kode = $ref->kode;
						$nama = $ref->nama;
					}
				}
				$result['tgl_daftar'] = $act_subs[$i]->tgl_subs;
				$result['tgl_akhir'] = (new \DateTime($act_subs[$i]->tgl_subs))->modify('+'.(30*$act_subs[$i]->packet->lama_paket).' day')->format('Y-m-d H:i:s');
				$result['kode_referal'] = $kode;
				$result['nama_referal'] = $nama;
				$result['packet'] = $packet;
				
				$result['payment'] = [
					'payment_url' => $act_subs[$i]->snap_url,
					'payment_uuid' => $act_subs[$i]->uuid
				];

				return response()->json([
					'message' => 'Success',
					'info' => 'Terdapat Transaksi Yang Masih Aktif',
					'data' => $result
				]);
			}
		}

		//$this->initPaymentGateway();
        //$snapToken = $subs->payment->snap_token;
        //if (empty($snapToken)) {
            // Jika snap token masih NULL, buat token snap dan simpan ke database
        $order_id = 'INV_'.
			date('Y').'_'.
			$this->numberToRomanRepresentation(date('m')).'_'.
			$this->numberToRomanRepresentation(date('d')).'_'.
			substr($request->user()->uuid, 0, 8).'_'.
			date('His');

        $params = [
            // 'enable_payments' => [ 
            
            // "bca_va", "bni_va", "bri_va", "other_va", "gopay", "indomaret",
            // "shopeepay"],
            'transaction_details' => [
                'order_id' => $order_id,
                'gross_amount' => $packet[0]->harga,
                'id' => $packet[0]->uuid,
                'price' => $packet[0]->harga,
                'quantity' => 1,
                'name' => $packet[0]->nama,
			],
			'customer_details' => [
				'first_name' => $user->uuid,
				'last_name' => $user->nama,
				'email' => $user->email,
				
			]
			
        ];
        // Set your Merchant Server Key
        \Midtrans\Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        // Set to Development/Sandbox Environment (default). Set to true for Production Environment (accept real transaction).
        \Midtrans\Config::$isProduction = true;
        // Set sanitization on (default)
        \Midtrans\Config::$isSanitized = true;
        // Set 3DS transaction for credit card to true
        \Midtrans\Config::$is3ds = true;

        $midtrans = \Midtrans\Snap::createTransaction($params);

		$orderDate = date('Y-m-d H:i:s');		
		$paymentDue = (new \DateTime($orderDate))->modify('+1 day')->format('Y-m-d H:i:s');
        $validation = new Helper\ValidationController('subs');
        $uuid1 = $validation->data['uuid'];

		//return $midtrans;

		$refId = '';
		$kode = '';
		$nama = '';

        $ref=Models\Reference::where('kode',$request->referal)->first();
		if($ref!=null || $ref != ''){
			$refId = $ref->id;
			$kode = $ref->kode;
			$nama = $ref->nama;
		}

		$data = [
			'id_user' => $user->id,
			'id_packet' => $packet[0]->id,
			'id_reference' => $refId,
			'harga' => $packet[0]->harga,
			'diskon' => 0,
			'tgl_subs' => $orderDate,
			'tgl_akhir_bayar' => (new \DateTime($orderDate))->modify('+1 day')->format('Y-m-d H:i:s'),
			'snap_token' => $midtrans->token,
			'snap_url' => $midtrans->redirect_url,
			'subs_status' => 'UNPAID',
			'uuid' => $order_id,
		];


        $packet = Models\Packet::select([
            'lama_paket',
            'harga',
            'status_aktif',
            'uuid',
            ])->where('uuid',$uuid)->first();
        //unset($class_cat['id']);
		

        $result['tgl_daftar'] = $orderDate;
        $result['tgl_akhir'] = (new \DateTime(date('Y-m-d')))->modify('+'.(30*$packet->lama_paket).' day')->format('Y-m-d H:i:s');
        $result['kode_referal'] = $kode;
        $result['nama_referal'] = $nama;
        $result['packet'] = $packet;

        $input = new Helper\InputController('subs',$data);

		$subs = Models\Subs::where('uuid',$order_id)->first();
		$result['payment'] = [
			'payment_url' => $subs->snap_url,
			'payment_uuid' => $subs->uuid
		];



		return response()->json([
			'message' => 'Success',
			'info' => 'Proses Langganan Telah Tersimpan. Segera Lakukan Pembayaran dalam waktu yang disediakan',
			'data' => $result
		]);
	}

	public function checkIosData(Request $request)
	{
		$user = $request->user();
		if(date_format(date_create($user->tgl_langganan_akhir),"Y/m/d") >= date('Y/m/d')){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Anda Masih Memiliki Langganan Aktif'
            ]);
		}
		if($user->jenis_akun == '0'){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Silahkan Lengkapi Data Anda Dahulu'
            ]);
		}
		if(count($user->detailStudent) == '0'){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Silahkan Lengkapi Data Anda Dahulu'
            ]);
		}
				
		return response()->json([
			'message' => 'Success',
			'info' => 'Proses Langganan Bisa Dilakukan',
			#'data' => $result
		]);
	}

	public function addIosData(Request $request)
	{
		if(!$uuid = $request->token){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Token tidak sesuai'
            ]);
        }
		$user = $request->user();
		
		$ref = [
			'kode'=>'',
			'nama'=>''
		];

		if(isset($request->referal)){
        	$ref=Models\Reference::where('kode',$request->referal)->get();
			if(count($ref)==0){
				return response()->json([
					'message' => 'Failed',
					'error' => 'Kode Referal Tidak Terdaftar'
				]);
			}
		}
        
        $packet = Models\Packet::where('uuid',$uuid)->get();
        if(count($packet) == 0){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Token tidak sesuai'
            ]);
        }

		//$this->initPaymentGateway();
        //$snapToken = $subs->payment->snap_token;
        //if (empty($snapToken)) {
            // Jika snap token masih NULL, buat token snap dan simpan ke database
        $order_id = 'INV_'.
			date('Y').'_'.
			$this->numberToRomanRepresentation(date('m')).'_'.
			$this->numberToRomanRepresentation(date('d')).'_'.
			substr($request->user()->uuid, 0, 8).'_'.
			date('His');

        $params = [
            // 'enable_payments' => [ 
            
            // "bca_va", "bni_va", "bri_va", "other_va", "gopay", "indomaret",
            // "shopeepay"],
            'transaction_details' => [
                'order_id' => $order_id,
                'gross_amount' => $packet[0]->harga,
                'id' => $packet[0]->uuid,
                'price' => $packet[0]->harga,
                'quantity' => 1,
                'name' => $packet[0]->nama,
			],
			'customer_details' => [
				'first_name' => $user->uuid,
				'last_name' => $user->nama,
				'email' => $user->email,
				
			]
			
        ];
        // // Set your Merchant Server Key
        // \Midtrans\Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        // // Set to Development/Sandbox Environment (default). Set to true for Production Environment (accept real transaction).
        // \Midtrans\Config::$isProduction = true;
        // // Set sanitization on (default)
        // \Midtrans\Config::$isSanitized = true;
        // // Set 3DS transaction for credit card to true
        // \Midtrans\Config::$is3ds = true;

        // $midtrans = \Midtrans\Snap::createTransaction($params);

		$orderDate = date('Y-m-d H:i:s');		
		$paymentDue = (new \DateTime($orderDate))->modify('+1 day')->format('Y-m-d H:i:s');
        $validation = new Helper\ValidationController('subs');
        $uuid1 = $validation->data['uuid'];

		//return $midtrans;

		$refId = '';
		$kode = '';
		$nama = '';

        $ref=Models\Reference::where('kode',$request->referal)->first();
		if($ref!=null || $ref != ''){
			$refId = $ref->id;
			$kode = $ref->kode;
			$nama = $ref->nama;
		}

		$data = [
			'id_user' => $user->id,
			'id_packet' => $packet[0]->id,
			'id_reference' => $refId,
			'harga' => $packet[0]->harga,
			'diskon' => 0,
			'tgl_subs' => $orderDate,
			'tgl_akhir_bayar' => (new \DateTime($orderDate))->modify('+ 1 day')->format('Y-m-d H:i:s'),
			'snap_token' => '-',
			'snap_url' => '-',
			'subs_status' => 'UNPAID',
			'uuid' => $order_id,
		];


        $input = new Helper\InputController('subs',$data);

		$subs = Models\Subs::where('uuid',$order_id)->first();
		$result['payment'] = [
			'payment_url' => $subs->snap_url,
			'payment_uuid' => $subs->uuid
		];

		//$paymentNotification = $request->paymentNotification;
		//$subs = Models\Subs::where('uuid', $order_id)->firstOrFail();

		
		$transaction = 'settlement';
		$type = $request->payment_type;
		$orderId = $order_id;
		//$fraud = $paymentNotification->fraud_status;

		

		$paymentStatus = null;
		if ($transaction == 'capture') {
			// For credit card transaction, we need to check whether transaction is challenge by FDS or not
			// if ($type == 'credit_card') {
			// 	if ($fraud == 'challenge') {
			// 		// TODO set payment status in merchant's database to 'Challenge by FDS'
			// 		// TODO merchant should decide whether this transaction is authorized or not in MAP
			// 		$paymentStatus = Models\Payment::CHALLENGE;
			// 	} else {
			// 		// TODO set payment status in merchant's database to 'Success'
			// 		$paymentStatus = Models\Payment::SUCCESS;
			// 	}
			// }
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

		if (in_array($paymentStatus, [Models\Payment::SUCCESS, Models\Payment::SETTLEMENT])) {
			$subs1 = Models\Subs::where('id',$subs->id)->update([
				'subs_status' => 'PAID'
			]);
			$user = Models\User::where('id',$subs->id_user)->update([
				'tgl_langganan_akhir' => (new \DateTime(date('Y-m-d')))->modify('+'.(30*$subs->packet->lama_paket).' day')->format('Y-m-d'),
			]);
		}	

		$paymentParams = [
			'id_subs' => $subs->id,
			'tgl_pembayaran' => date("Y/m/d H:i:s"),
			'transaction_id' => $order_id,
			'method' => 'applepay',
			'status' => $paymentStatus,
			'amount' => $request->gross_amount,
			'token' => $order_id,
			'payloads' => $request->payload,
			'payment_type' => $request->payment_type,
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

	public function detailByUser(Request $request){
		$user = $request->user();
		$subs = Models\Subs::where('id_user',$user->id)->get();

		$result = [];
		for($i=0;$i<count($subs);$i++){
			$status = 'TUNGGU';
			if($subs[$i]->subs_status=='PAID'){
				$status = 'BERHASIL';
			}if($subs[$i]->subs_status=='UNPAID' && date_format(date_create($subs[$i]->tgl_akhir_bayar),"Y/m/d H:i:s") < date('Y/m/d H:i:s')){
				$status = 'GAGAL';
			}
			$arr = [
				'lama_paket'=>$subs[$i]->packet->lama_paket,
				'harga'=>$subs[$i]->harga,
				'tgl_subs'=>$subs[$i]->tgl_subs,
				'subs_status'=>$status,
				'subs_uuid'=>$subs[$i]->uuid,
			];
			$result[$i] = $arr;
		}
		$res['detail'] = $result;

		return response()->json([
			'message'=>'Success',
			'data'=> $result,
			'data_web'=> $res,
		]);
	}

	public function detailSubs(Request $request){
		$user = $request->user();

		if(!$uuid = $request->token){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Token tidak sesuai'
            ]);
        }
		$subs = Models\Subs::where('uuid',$request->token)->get();
		if(count($subs)==0){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Token tidak sesuai'
            ]);
        }

		$result = [];
		$packet = Models\Packet::select([
            'lama_paket',
            'harga',
            'status_aktif',
            'uuid',
            ])->where('id',$subs[0]->id_packet)->first();
        //unset($class_cat['id']);
		$refId = '';
		$kode = '';
		$nama = '';
		//return $subs[0];

		if($subs[0]->id_reference!=null || $subs[0]->id_reference!= ''){
			$ref=Models\Reference::where('id',$subs[0]->id_reference)->first();
			if($ref!=null || $ref != ''){
				$refId = $ref->id;
				$kode = $ref->kode;
				$nama = $ref->nama;
			}
		}

		
        $result['tgl_daftar'] = $subs[0]->tgl_subs;
        $result['tgl_akhir'] = (new \DateTime($subs[0]->tgl_subs))->modify('+'.(30*$subs[0]->packet->lama_paket).' day')->format('Y-m-d H:i:s');
        $result['kode_referal'] = $kode;
        $result['nama_referal'] = $nama;
        $result['packet'] = $packet;

		$subs = Models\Subs::where('uuid',$uuid)->first();
		$act_subs = Models\Subs::select(['tgl_subs','tgl_akhir_bayar','uuid','snap_url'])
					->where('id_packet',$packet->id)
					->where('id_user',$user->id)->get();
		for($i=0;$i<count($act_subs);$i++){
			if(date_format(date_create($act_subs[$i]->tgl_akhir_bayar),"Y/m/d H:i:s") > date('Y/m/d H:i:s')){
				$result['payment'] = [
					'payment_url' => $subs->snap_url,
					'payment_uuid' => $subs->uuid
				];
			}
		}
		

		return response()->json([
			'message'=>'Success',
			'data'=> $result]);
	}


}
