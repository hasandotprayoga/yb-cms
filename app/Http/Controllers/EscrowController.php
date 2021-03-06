<?php

namespace App\Http\Controllers;

use App\Libraries\Orderonline;

class EscrowController extends Controller
{
    
    public function index()
    {

        $escrow = app(Orderonline::class)->getEscrow();

        return view('escrow', [
            'data' => $escrow
        ]);
    }
}
