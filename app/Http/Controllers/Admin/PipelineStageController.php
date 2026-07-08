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

    public function reorder(Request $request)
    {
        $request->validate(['order' => 'required|array']);
        foreach ($request->order as $index => $id) {
            PipelineStage::where('id', $id)->update(['order' => $index + 1]);
        }
        return response()->json(['success' => true]);
    }
}
