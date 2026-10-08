<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CaseFilterRequest;
use App\Http\Requests\CaseRequest;
use App\Models\MedicalCase;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Gate;

class CaseController extends Controller
{
    use ApiResponse;

    public function index(CaseFilterRequest $request)
    {
        $page = MedicalCase::with('creator:id,name')
            ->filter($request->validated())
            ->paginate(10)
            ->withQueryString();

        return $this->success($page->items(), 'Cases retrieved.', 200, [
            'meta' => [
                'current_page' => $page->currentPage(),
                'last_page' => $page->lastPage(),
                'per_page' => $page->perPage(),
                'total' => $page->total(),
            ],
        ]);
    }

    public function store(CaseRequest $request)
    {
        $case = MedicalCase::create($request->validated() + ['created_by' => $request->user()->id]);

        return $this->success($case->load('creator:id,name'), 'Case created.', 201);
    }

    public function show(MedicalCase $case)
    {
        Gate::authorize('view', $case);

        return $this->success($case->load('creator:id,name'), 'Case retrieved.');
    }

    public function update(CaseRequest $request, MedicalCase $case)
    {
        $case->update($request->validated());

        return $this->success($case->load('creator:id,name'), 'Case updated.');
    }

    public function destroy(MedicalCase $case)
    {
        Gate::authorize('delete', $case);

        $case->delete();

        return $this->success(null, 'Case deleted.');
    }
}
