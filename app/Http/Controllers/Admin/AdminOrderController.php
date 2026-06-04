<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class AdminOrderController extends Controller
{
    public function index()
    {
        $orders = [
            [
                'id' => 1,
                'technieker' => 'Samia',
                'status' => 'Nieuw'
            ],
            [
                'id' => 2,
                'technieker' => 'Yasmina',
                'status' => 'Geleverd'
            ]
        ];

        return view('admin.orders.index', compact('orders'));
    }
}
