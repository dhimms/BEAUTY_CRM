<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
// memanggil file ImportExportService yang ada di dalam folder Service
use App\Services\ImportExportService;
use Illuminate\Http\Request;

class ImportExportController extends Controller        
{            
    //== construct adalah sebuah method yang wajib digunkan untuk mengenali sebuah class yang kita buat sendiri ==// 
    // disini kita mengunakan constructor untuk memanggil class ImportExportService agar dikenali oleh semua method di dalam class ImportExportController ==// 
    public function __construct(private ImportExportService $service) {} 
     
    public function import(Request $request)              
    {      
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:10240'],
        ]);
           
        // memanggil method importLeads yang ada di dalam class ImportExportService
        // request()->file('file') = mengambil file yang diupload dari form
        $result = $this->service->importLeads($request->file('file'));

        // ini adalah proses untuk mengambil error yang terjadi saat import
        // pertama kita mengambil jumlah error yang terjadi dengan memanggil nama array 'failure_count'
        // lalu kita hitung error nya ada berapa , jika > 0 berarti ada error
        // lalu menampilkan pesan error sesuai dengan data error yang terjadi 
        if ($result['failure_count'] > 0) { 
            $messages = collect($result['failures'])->map(
                fn($f) => "Baris {$f->row()}: " . implode(', ', $f->errors())
            )->implode(' | ');
 
            return redirect()->route('admin.leads.index')
                ->with('error', "Import selesai dengan {$result['failure_count']} error: {$messages}");
        }

        return redirect()->route('admin.leads.index')
            ->with('success', 'Import leads berhasil.');
    }

    public function export(Request $request)
    {
        return $this->service->exportLeads($request->only([
            'search', 'status', 'source', 'qualification', 'assigned_to', 'date_from', 'date_to', 'period',
        ]));
    }

    public function downloadTemplate()
    {
        return $this->service->downloadLeadTemplate();
    }
}
