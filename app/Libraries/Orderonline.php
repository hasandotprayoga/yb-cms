<?php

namespace App\Libraries;

use Illuminate\Support\Facades\Http;

class Orderonline
{
    private $email;
    private $password;

    private $token;

    protected $order = [];
    protected $escrow = [];
    
    public function __construct()
    {
        $this->email = 'giokevin76@gmail.com';
        $this->password = 'kevin123!';

        $this->login();
        $this->getOrder();
    }

    public function getEscrow()
    {
        $params = [
            'data' => []
        ];

        foreach ($this->order as $k => $v) {
            $params['data'][] = [
                'amount' => $v['gross_revenue'],
                'referenceTimestamp' => $v['created_at']
            ];
        }

        $response = Http::withHeaders([
            'X-Requested-With' => 'XMLHttpRequest'
        ])->post('https://gateway.yubiapi.net/v1/application/find/escrow', $params);

        if ($response->ok()) {
            $data = $response->json();
            $escrow = $data['response']['results'];

            $result = [];

            foreach ($escrow as $k => $v) {
                $key = array_search($v['amount'], array_column($this->order, 'gross_revenue'));
                $v['orderId'] = $this->order[$key]['order_id'];
                $result[$k] = $v;
            }

            $this->escrow = $result;
        }

        return $this->escrow;
    }

    public function getOrder()
    {

        $token = $this->token;

        if ($token) {

            $params = [
                'limit' => 1000,
                'sort_by' => 'created_at',
                'sort' => 'desc',
                'since' => '2020-02-20',
                'until' => date('Y-m-d'),
                'payment_status' => 'unpaid'
            ];

            $response = Http::withHeaders([
                'authorization' => "Bearer $token"
            ])->get('https://api.orderonline.id/submission', $params);
    
            if ($response->ok()) {
                $data = $response->json()['data'];
                $this->order = $data;
            }
        }

    }

    protected function login()
    {
        $response = Http::post('https://api.orderonline.id/auth', [
            'email' => $this->email,
            'password' => $this->password
        ]);
        
        if ($response->ok()) {
            $data = $response->json();
            
            if ($data['message'] == 'Login success') {
                $this->token = $data['data']['access_token'];
            }
        }
    }
}
