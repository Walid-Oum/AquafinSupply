<?php

namespace App\Http\Controllers\Userzone;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
         /**
     * Toont het winkelmandje van de technieker.
     */
        return view('userzone.orders.cart');
    }
}
