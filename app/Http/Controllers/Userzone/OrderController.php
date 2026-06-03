<?php

namespace App\Http\Controllers\Userzone;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OrderController extends Controller
{
     public function index()
    {
         /**
     * Toont een overzicht van alle bestellingen.
     */
        return view('userzone.orders.index');
    }

    public function show($id)
    {
        /**
     * Toont de details van een specifieke bestelling.
     */
        return view('userzone.orders.show', compact('id'));
    }
}
