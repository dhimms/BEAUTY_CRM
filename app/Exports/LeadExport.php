<?php

namespace App\Exports;

use App\Models\Lead;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LeadExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    /**
     * Resolve date range dari pilihan periode.
     */
    protected function resolveDateRange(): array
    {
        $period    = $this->filters['period'] ?? null;
        $dateFrom  = $this->filters['date_from'] ?? null;
        $dateTo    = $this->filters['date_to'] ?? null;

        if ($period) {
            $dateFrom = match($period) {
                'today'      => now()->toDateString(),
                'this_week'  => now()->startOfWeek()->toDateString(),
                'this_month' => now()->startOfMonth()->toDateString(),
                'this_year'  => now()->startOfYear()->toDateString(),
                default      => null,
            };
            $dateTo = now()->toDateString();
        }

        return [$dateFrom, $dateTo];
    }

    public function query()
    {
        [$dateFrom, $dateTo] = $this->resolveDateRange();

        return Lead::query()
            ->with(['source', 'assignedUser', 'creator'])
            ->search($this->filters['search'] ?? null)
            ->filterStatus($this->filters['status'] ?? null)
            ->filterSource($this->filters['source'] ?? null)
            ->filterQualification($this->filters['qualification'] ?? null)
            ->filterAssigned($this->filters['assigned_to'] ?? null)
            ->when($dateFrom, fn($q, $v) => $q->whereDate('created_at', '>=', $v))
            ->when($dateTo,   fn($q, $v) => $q->whereDate('created_at', '<=', $v))
            ->latest();
    }

    public function headings(): array
    {
        return [
            'ID', 'Nama', 'Email', 'Telepon', 'Alamat',
            'Sumber Lead', 'Status', 'Kualifikasi', 'Ditugaskan Ke',
            'Catatan', 'Dibuat Oleh', 'Tanggal Dibuat',
        ];
    }

    public function map($lead): array
    {
        return [
            $lead->id,
            $lead->name,
            $lead->email,
            $lead->phone,
            $lead->address,
            $lead->source?->name,
            ucfirst($lead->status),
            $lead->qualification ? ucfirst($lead->qualification) : '-',
            $lead->assignedUser?->name ?? '-',
            $lead->notes,
            $lead->creator?->name ?? '-',
            $lead->created_at->format('d/m/Y H:i'),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}

