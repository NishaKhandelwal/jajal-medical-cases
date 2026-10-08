<?php

namespace App\Http\Controllers;

use App\Http\Requests\CaseFilterRequest;
use App\Http\Requests\CaseRequest;
use App\Models\MedicalCase;
use Illuminate\Support\Facades\Gate;

class CaseController extends Controller
{
    public function index(CaseFilterRequest $request)
    {
        $cases = MedicalCase::with('creator:id,name')
            ->filter($request->validated())
            ->paginate(10)
            ->withQueryString();

        return view('cases.index', [
            'cases' => $cases,
            'statuses' => MedicalCase::STATUSES,
            'priorities' => MedicalCase::PRIORITIES,
        ]);
    }

    public function create()
    {
        Gate::authorize('create', MedicalCase::class);

        $next = 'CASE-' . str_pad((string) ((MedicalCase::max('id') ?? 0) + 1), 4, '0', STR_PAD_LEFT);
        $case = new MedicalCase(['case_number' => $next, 'status' => 'Draft', 'priority' => 'Medium']);

        return view('cases.create', [
            'case' => $case,
            'statuses' => MedicalCase::STATUSES,
            'priorities' => MedicalCase::PRIORITIES,
        ]);
    }

    public function store(CaseRequest $request)
    {
        // created_by comes from the session, never from user input.
        MedicalCase::create($request->validated() + ['created_by' => $request->user()->id]);

        return redirect()->route('cases.index')->with('success', 'Case created.');
    }

    public function show(MedicalCase $case)
    {
        Gate::authorize('view', $case);

        return view('cases.show', ['case' => $case->load('creator:id,name')]);
    }

    public function edit(MedicalCase $case)
    {
        Gate::authorize('update', $case);

        return view('cases.edit', [
            'case' => $case,
            'statuses' => MedicalCase::STATUSES,
            'priorities' => MedicalCase::PRIORITIES,
        ]);
    }

    public function update(CaseRequest $request, MedicalCase $case)
    {
        $case->update($request->validated());

        return redirect()->route('cases.show', $case)->with('success', 'Case updated.');
    }

    public function destroy(MedicalCase $case)
    {
        // Server-side check: hiding the button is not enough.
        Gate::authorize('delete', $case);

        $case->delete();

        return redirect()->route('cases.index')->with('success', 'Case deleted.');
    }
}
