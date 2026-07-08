<?php

namespace App\Imports;

use App\Models\Lead;
use App\Models\LeadSource;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class LeadImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure, WithBatchInserts, WithChunkReading
{
    use SkipsFailures;

    public function model(array $row): ?Lead
    {
        $source = LeadSource::where('name', $row['lead_source'] ?? '')->first();

        return new Lead([
            'name'           => $row['name'],
            'email'          => $row['email'] ?? null,
            'phone'          => $row['phone'],
            'address'        => $row['address'] ?? null,
            'lead_source_id' => $source?->id ?? LeadSource::first()?->id,
            'notes'          => $row['notes'] ?? null,
            'status'         => 'new',
            'created_by'     => Auth::id(),
        ]);
    }

    public function rules(): array
    {
        return [
            'name'  => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email',
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'name.required'  => 'Kolom "name" wajib diisi.',
            'phone.required' => 'Kolom "phone" wajib diisi.',
            'email.email'    => 'Format email tidak valid.',
        ];
    }

    public function batchSize(): int { return 100; }
    public function chunkSize(): int { return 500; }
}
