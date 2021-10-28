use App\Services\Midtrans\CreateSnapTokenService; // => letakkan pada bagian atas class
 
public function show(Order $order)
    {
        $snapToken = $order->snap_token;
        if (empty($snapToken)) {
            // Jika snap token masih NULL, buat token snap dan simpan ke database
 
            $midtrans = new CreateSnapTokenService($order);
            $snapToken = $midtrans->getSnapToken();
 
            $order->snap_token = $snapToken;
            $order->save();
        }
 
        return view('orders.show', compact('order', 'snapToken'));
    }