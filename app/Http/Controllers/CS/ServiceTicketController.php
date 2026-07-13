<?php

namespace App\Http\Controllers\CS;

use App\Http\Controllers\Controller;
use App\Http\Requests\CS\StoreTicketRequest;
use App\Http\Requests\CS\UpdateTicketRequest;
use App\Models\Customer;
use App\Models\ServiceTicket;
use App\Services\CustomerService;
use Illuminate\Http\Request;

class ServiceTicketController extends Controller
{
    public function __construct(
        private CustomerService $customerService
    ) {}

    public function index(Request $request)
    {
        $tickets = $this->customerService->getTickets(
            $request->only(['search', 'status', 'priority', 'category', 'assigned_to'])
        );
        $csUsers = $this->customerService->getCsUsers();

        return view('cs.tickets.index', compact('tickets', 'csUsers'));
    }

    public function create()
    {
        $customers = Customer::orderBy('name')->get();
        $csUsers = $this->customerService->getCsUsers();

        return view('cs.tickets.create', compact('customers', 'csUsers'));
    }

    public function store(StoreTicketRequest $request)
    {
        $data = $request->validated();
        if (!isset($data['assigned_to'])) {
            $data['assigned_to'] = auth()->id();
        }

        $ticket = $this->customerService->createTicket($data);

        return redirect()->route('cs.tickets.show', $ticket)
            ->with('success', "Ticket {$ticket->ticket_number} berhasil dibuat.");
    }

    public function show(ServiceTicket $ticket)
    {
        $ticket = $this->customerService->getTicketDetail($ticket->id);
        return view('cs.tickets.show', compact('ticket'));
    }

    public function edit(ServiceTicket $ticket)
    {
        $ticket->load(['customer', 'assignedUser']);
        $customers = Customer::orderBy('name')->get();
        $csUsers = $this->customerService->getCsUsers();

        return view('cs.tickets.edit', compact('ticket', 'customers', 'csUsers'));
    }

    public function update(UpdateTicketRequest $request, ServiceTicket $ticket)
    {
        $this->customerService->updateTicket($ticket, $request->validated());

        return redirect()->route('cs.tickets.show', $ticket)
            ->with('success', 'Ticket berhasil diperbarui.');
    }

    public function destroy(ServiceTicket $ticket)
    {
        $this->customerService->deleteTicket($ticket);

        return redirect()->route('cs.tickets.index')
            ->with('success', "Ticket {$ticket->ticket_number} berhasil dihapus.");
    }

    public function updateStatus(Request $request, ServiceTicket $ticket)
    {
        $request->validate(['status' => 'required|in:open,in_progress,resolved,closed']);

        try {
            $ticket = $this->customerService->updateTicketStatus($ticket, $request->status);
            return response()->json([
                'success' => true,
                'message' => 'Status berhasil diperbarui.',
                'status' => $ticket->status,
                'status_color' => $ticket->status_color,
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}