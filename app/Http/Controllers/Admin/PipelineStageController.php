<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PipelineStageRequest;
use App\Models\PipelineStage;
use Illuminate\Http\Request;

class PipelineStageController extends Controller
{
    public function index()
    {
        $stages = PipelineStage::withCount('deals')->ordered()->get();
        return view('admin.pipeline-stages.index', compact('stages'));
    }

    public function create()
    {
        $maxOrder = PipelineStage::max('order') ?? 0;
        return view('admin.pipeline-stages.create', compact('maxOrder'));
    }

    public function store(PipelineStageRequest $request)
    {
        
        $data = $request->validated();
        if (!isset($data['order'])) {
            $data['order'] = (PipelineStage::max('order') ?? 0) + 1;
        }
        PipelineStage::create($data);
        return redirect()->route('admin.pipeline-stages.index')
            ->with('success', 'Pipeline stage berhasil ditambahkan.');
    }

    public function edit(PipelineStage $pipelineStage)
    {
        return view('admin.pipeline-stages.edit', compact('pipelineStage'));
    }

    public function update(PipelineStageRequest $request, PipelineStage $pipelineStage)
    {
        $pipelineStage->update($request->validated());
        return redirect()->route('admin.pipeline-stages.index')
            ->with('success', 'Pipeline stage berhasil diperbarui.');
    }

    public function destroy(PipelineStage $pipelineStage)
    {
        if ($pipelineStage->deals()->exists()) {
            return back()->with('error', 'Stage tidak bisa dihapus karena memiliki deal terkait.');
        }
        $pipelineStage->delete();
        return redirect()->route('admin.pipeline-stages.index')
            ->with('success', 'Pipeline stage berhasil dihapus.');
    }

    // ini adalah method untuk mengupdate urutan data (pada menu pipeline settings degnan cara geser")
    // pertama kita menangkap pergeseran pipelane yang kita lakukan dengan Request
    // kedua validasi apakah order ada dan berisi array, jika tidak maka akan error
    // ketiga melakukan looping untuk membaca urutan array terbaru (karena pipelane settings nya telah kita ubah )
    // keempat melakukan update berdasarkan id dan order terbaru
    public function reorder(Request $request)
    {   // 'order' ini adalah variabel dari js yang digunakan untuk menyimpan urutan array
        $request->validate(['order' => 'required|array']);
        // $index ini adalah key dari array yang urutannya berubah, sedangkan $id adalah nilai dari key tersebut (id dari pipeline stage)
        foreach ($request->order as $index => $id) {
            // where('id', $id) ini adalah untuk mencari id yang sama dengan id pada database
            // update(['order' => $index + 1]) ini adalah untuk mengupdate order berdasarkan urutan array
            // jadi pipelines yang kita geser akan terupdate urutannya sesuai dengan urutan array
            PipelineStage::where('id', $id)->update(['order' => $index + 1]);
        }
        // response()->json(['success' => true]); ini adalah untuk mengirimkan data ke js
        // agar js bisa mengetahui bahwa update berhasil
        return response()->json(['success' => true]);
    }
} 
