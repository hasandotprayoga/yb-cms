<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Libraries\Orderonline;

class EscrowController extends Controller
{
    
    public function index()
    {
        $escrow = app(Orderonline::class)->getEscrow();

        return response()->json($escrow);
    }
}
