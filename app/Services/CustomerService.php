<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\Customer;
use App\Models\ServiceTicket;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class CustomerService
{
    // ─── Dashboard ────────────────────────────────────

    public function getDashboardData(): array
    {
        $today = Carbon::today();

        $totalCustomers = Customer::count();
        $activeCustomers = Customer::active()->count();
        $openTickets = ServiceTicket::open()->count();

        $todayFollowUps = Activity::with(['user', 'activitable'])
            ->whereNotNull('follow_up_date')
            ->where('follow_up_status', 'pending')
            ->whereDate('follow_up_date', $today)
            ->orderBy('follow_up_date')
            ->get();

        $overdueFollowUps = Activity::with(['user', 'activitable'])
            ->overdueFollowUps()
            ->get();

        $recentTickets = ServiceTicket::with(['customer', 'assignedUser'])
            ->latest()
            ->take(5)
            ->get();

        // Ticket by priority for doughnut chart
        $ticketsByPriority = ServiceTicket::open()
            ->selectRaw("priority, COUNT(*) as count")
            ->groupBy('priority')
            ->pluck('count', 'priority')
            ->toArray();

        $newCustomersThisMonth = Customer::whereMonth('created_at', $today->month)
            ->whereYear('created_at', $today->year)
            ->count();

        return compact(
            'totalCustomers',
            'activeCustomers',
            'openTickets',
            'todayFollowUps',
            'overdueFollowUps',
            'recentTickets',
            'ticketsByPriority',
            'newCustomersThisMonth'
        );
    }

    // ─── Customers ────────────────────────────────────

    public function getCustomers(array $filters = []): LengthAwarePaginator
    {
        return Customer::with('csUser')
            ->search($filters['search'] ?? null)
            ->when($filters['status'] ?? null, fn($q, $s) => $q->where('status', $s))
            ->when($filters['user_id'] ?? null, fn($q, $u) => $q->where('user_id', $u))
            ->latest()
            ->paginate(15)
            ->withQueryString();
    }

    public function createCustomer(array $data): Customer
    {
        if (isset($data['tags']) && is_string($data['tags'])) {
            $data['tags'] = array_map('trim', explode(',', $data['tags']));
        }

        return Customer::create($data);
    }

    public function updateCustomer(Customer $customer, array $data): Customer
    {
        if (isset($data['tags']) && is_string($data['tags'])) {
            $data['tags'] = array_map('trim', explode(',', $data['tags']));
        }

        $customer->update($data);
        return $customer->fresh();
    }

    public function getCustomerDetail(int $id): Customer
    {
        return Customer::with([
            'csUser',
            'lead',
            'serviceTickets' => fn($q) => $q->with('assignedUser')->latest(),
            'activities' => fn($q) => $q->with('user')->latest(),
        ])->findOrFail($id);
    }

    // ─── Service Tickets ──────────────────────────────

    public function getTickets(array $filters = []): LengthAwarePaginator
    {
        return ServiceTicket::with(['customer', 'assignedUser'])
            ->search($filters['search'] ?? null)
            ->when($filters['status'] ?? null, fn($q, $s) => $q->where('status', $s))
            ->when($filters['priority'] ?? null, fn($q, $p) => $q->where('priority', $p))
            ->when($filters['category'] ?? null, fn($q, $c) => $q->where('category', $c))
            ->when($filters['assigned_to'] ?? null, fn($q, $a) => $q->where('assigned_to', $a))
            ->latest()
            ->paginate(15)
            ->withQueryString();
    }

    public function createTicket(array $data): ServiceTicket
    {
        return ServiceTicket::create($data);
    }

    public function updateTicket(ServiceTicket $ticket, array $data): ServiceTicket
    {
        $ticket->update($data);
        return $ticket->fresh();
    }

    public function deleteTicket(ServiceTicket $ticket): bool
    {
        return $ticket->delete();
    }

    public function updateTicketStatus(ServiceTicket $ticket, string $status): ServiceTicket
    {
        $validTransitions = [
            'open' => ['in_progress'],
            'in_progress' => ['resolved', 'open'],
            'resolved' => ['closed', 'in_progress'],
            'closed' => ['open'],
        ];

        $allowed = $validTransitions[$ticket->status] ?? [];

        if (!in_array($status, $allowed)) {
            throw new \InvalidArgumentException(
                "Cannot transition from '{$ticket->status}' to '{$status}'."
            );
        }

        $ticket->update(['status' => $status]);
        return $ticket->fresh();
    }

    public function getTicketDetail(int $id): ServiceTicket
    {
        return ServiceTicket::with([
            'customer',
            'assignedUser',
            'activities' => fn($q) => $q->with('user')->latest(),
        ])->findOrFail($id);
    }

    // ─── Follow-ups ───────────────────────────────────

    public function getFollowUps(array $filters = []): array
    {
        $baseQuery = Activity::with(['user', 'activitable'])
            ->whereNotNull('follow_up_date');

        $pending = (clone $baseQuery)
            ->where('follow_up_status', 'pending')
            ->when($filters['from'] ?? null, fn($q, $d) => $q->whereDate('follow_up_date', '>=', $d))
            ->when($filters['to'] ?? null, fn($q, $d) => $q->whereDate('follow_up_date', '<=', $d))
            ->orderBy('follow_up_date')
            ->get();

        $overdue = (clone $baseQuery)
            ->where('follow_up_status', 'pending')
            ->whereDate('follow_up_date', '<', Carbon::today())
            ->orderBy('follow_up_date')
            ->get();

        $completed = (clone $baseQuery)
            ->where('follow_up_status', 'done')
            ->when($filters['from'] ?? null, fn($q, $d) => $q->whereDate('follow_up_date', '>=', $d))
            ->when($filters['to'] ?? null, fn($q, $d) => $q->whereDate('follow_up_date', '<=', $d))
            ->latest('updated_at')
            ->take(20)
            ->get();

        return compact('pending', 'overdue', 'completed');
    }

    public function createFollowUp(array $data): Activity
    {
        return Activity::create([
            'user_id' => auth()->id(),
            'activitable_type' => Customer::class,
            'activitable_id' => $data['customer_id'],
            'type' => $data['follow_up_type'] ?? 'call',
            'subject' => $data['subject'] ?? 'Follow-up',
            'follow_up_date' => $data['follow_up_date'],
            'follow_up_type' => $data['follow_up_type'] ?? 'call',
            'follow_up_notes' => $data['follow_up_notes'] ?? null,
            'follow_up_status' => 'pending',
            'activity_date' => now(),
        ]);
    }

    public function completeFollowUp(Activity $activity): Activity
    {
        $activity->update([
            'follow_up_status' => 'done',
            'result' => 'connected',
        ]);

        return $activity->fresh();
    }

    // ─── Activities ───────────────────────────────────

    public function logActivity(array $data): Activity
    {
        $typeMap = [
            'customer' => Customer::class,
            'ticket' => ServiceTicket::class,
        ];

        return Activity::create([
            'user_id' => auth()->id(),
            'activitable_type' => $typeMap[$data['activitable_type']] ?? Customer::class,
            'activitable_id' => $data['activitable_id'],
            'type' => $data['type'],
            'subject' => $data['subject'] ?? null,
            'description' => $data['description'] ?? null,
            'duration' => $data['duration'] ?? null,
            'result' => $data['result'] ?? null,
            'activity_date' => $data['activity_date'] ?? now(),
        ]);
    }

    // ─── Helpers ──────────────────────────────────────

    public function getCsUsers(): Collection
    {
        return User::role('Customer Service')->where('is_active', true)->get();
    }
}
