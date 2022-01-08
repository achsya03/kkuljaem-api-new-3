<?php
 
namespace App\Services\Midtrans;
 
use Midtrans\Snap;
 
class CreateSnapTokenService extends Midtrans
{
    protected $subs;
 
    public function __construct($subs)
    {
        parent::__construct();
 
        $this->subs = $subs;
    }
 
    public function getSnapToken()
    {
        $params = [
            'transaction_details' => [
                'order_id' => $this->subs['order_id'],
                'gross_amount' => $this->subs['gross_amount'],
            ],
            'item_details' => [
                [
                    'id' => $this->subs['id'],
                    'price' => $this->subs['price'],
                    'quantity' => $this->subs['quantity'],
                    'name' => $this->subs['name'],
                ]
            ],
            'customer_details' => [
                'first_name' => $this->subs['first_name'],
                'email' => $this->subs['email'],
                //'phone' => '',
            ]
        ];
 
        $snapToken = Snap::getSnapToken($params);
 
        return $snapToken;
    }
}
