<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class EscrowController extends Controller
{

    protected $email = 'giokevin76@gmail.com';
    protected $password = 'kevin123!';

    protected $token;

    protected $escrow = [];
    protected $unpaid = [];
    
    public function index()
    {
        $this->login();
        $this->unpaid();
        $this->findEscrow();

        return view('escrow', [
            'data' => $this->escrow
        ]);
    }

    protected function findEscrow()
    {

        $params = [
            'data' => []
        ];

        foreach ($this->unpaid as $k => $v) {
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
                $key = array_search($v['amount'], array_column($this->unpaid, 'gross_revenue'));
                $v['orderId'] = $this->unpaid[$key]['order_id'];
                $result[$k] = $v;
            }

            $this->escrow = $result;
        }
    }

    protected function unpaid()
    {

        $token = $this->token;

        $response = Http::withHeaders([
            'authorization' => "Bearer $token"
        ])->get('https://api.orderonline.id/submission', [
            'limit' => 1000,
            'sort_by' => 'created_at',
            'sort' => 'desc',
            'since' => '2020-02-20',
            'until' => date('Y-m-d'),
            'payment_status' => 'unpaid'
        ]);

        if ($response->ok()) {
            $data = $response->json()['data'];
            $this->unpaid = $data;
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
