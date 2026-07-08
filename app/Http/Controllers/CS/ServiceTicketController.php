<?php

namespace App\Http\Controllers\CS;

use App\Http\Controllers\Controller;

class ServiceTicketController extends Controller
{
    public function index()
    {
        return view('cs.tickets.index');
    }
    public function create()
    {
        return view('cs.tickets.create');
    }
    public function store()
    { /* TODO */
    }
    public function show($id)
    {
        return view('cs.tickets.show');
    }
    public function edit($id)
    {
        return view('cs.tickets.edit');
    }
    public function update()
    { /* TODO */
    }
    public function destroy()
    { /* TODO */
    }
    public function updateStatus()
    { /* TODO */
    }
}