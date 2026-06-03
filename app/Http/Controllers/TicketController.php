<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function index(){
        $tickets = Ticket::where('user_id', auth()->user()->id)->get();
        return view('tickets.index', ['tickets' => $tickets]);
    }

    public function create(){
        return view('tickets.create');
    }
    public function store(Request $request){
        $validated = $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
        ]);
        Ticket::create([
            'user_id' => auth()->user()->id,
            'subject' => $request->subject,
            'description' => $request->description,
            'order_id' => null,

        ]);
        return redirect()->route('tickets.index')->with('success', 'Ticket created successfully');

    }

    //
}
