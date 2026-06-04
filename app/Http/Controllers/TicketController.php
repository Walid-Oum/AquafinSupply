<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Ticket;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function index(){
        $tickets = Ticket::where('user_id', auth()->user()->id)->get();
        return view('tickets.index', ['tickets' => $tickets]);
    }

    public function create(){
        $orders = Order::where('user_id', auth()->user()->id)->get();
        return view('tickets.create', ['orders' => $orders]);
    }
    public function store(Request $request){
        $validated = $request->validate([
            'order_id' => ['required', 'exists:orders,id'],
            'subject' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
        ]);
        $order = Order::where('id', $validated['order_id'])->where('user_id', auth()->user()->id)->firstOrFail();
        Ticket::create([
            'user_id' => auth()->user()->id,
            'order_id' => $order->id,
            'subject' => $validated['subject'],
            'description' => $validated['description'],


        ]);
        return redirect()->route('tickets.index')->with('success', 'Ticket created successfully');

    }


    public function all(){
        $tickets = Ticket::with(['user', 'order'])->get();
        return view('tickets.all', ['tickets' => $tickets]);
    }

    //
}
