@php
    $status = [
        'Draft'     => ['bg-gray-50 text-gray-700 ring-gray-200',         'bg-gray-400'],
        'Planning'  => ['bg-blue-50 text-blue-700 ring-blue-200',         'bg-blue-500'],
        'In Review' => ['bg-amber-50 text-amber-700 ring-amber-200',      'bg-amber-500'],
        'Approved'  => ['bg-green-50 text-green-700 ring-green-200',      'bg-green-500'],
        'Completed' => ['bg-emerald-50 text-emerald-700 ring-emerald-200','bg-emerald-600'],
        'Cancelled' => ['bg-red-50 text-red-700 ring-red-200',            'bg-red-500'],
    ];
    $priority = [
        'Low'    => 'bg-slate-50 text-slate-600 ring-slate-200',
        'Medium' => 'bg-amber-50 text-amber-700 ring-amber-200',
        'High'   => 'bg-red-50 text-red-700 ring-red-200',
    ];
    [$sb, $sd] = $status[$case->status] ?? $status['Draft'];
    $label = 'text-xs font-semibold uppercase tracking-wider text-gray-500';
    $value = 'mt-1 text-sm font-medium text-gray-900';
@endphp
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <div class="flex flex-wrap items-center gap-3">
                <h2 class="font-bold text-2xl text-gray-900 tracking-tight">{{ $case->case_number }}</h2>
                <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium ring-1 ring-inset {{ $sb }}">
                    <span class="h-1.5 w-1.5 rounded-full {{ $sd }}"></span>{{ $case->status }}
                </span>
            </div>
            <a href="{{ route('cases.index') }}" class="text-sm font-medium text-gray-500 hover:text-gray-800">← Back to list</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
            @if (session('success'))
                <div class="flex items-center gap-2 rounded-lg bg-green-50 px-4 py-3 text-sm text-green-800 ring-1 ring-green-200">
                    <span class="font-bold">✓</span> {{ session('success') }}
                </div>
            @endif

            <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-200">
                <div class="p-6 sm:p-8">
                    <h3 class="text-base font-semibold text-gray-900">Case information</h3>
                    <dl class="mt-5 grid grid-cols-1 gap-x-8 gap-y-6 sm:grid-cols-2">
                        <div><dt class="{{ $label }}">Case Number</dt><dd class="{{ $value }}">{{ $case->case_number }}</dd></div>
                        <div><dt class="{{ $label }}">Patient Reference</dt><dd class="{{ $value }}">{{ $case->patient_reference }}</dd></div>
                        <div><dt class="{{ $label }}">Surgeon</dt><dd class="{{ $value }}">{{ $case->surgeon_name }}</dd></div>
                        <div><dt class="{{ $label }}">Implant Type</dt><dd class="{{ $value }}">{{ $case->implant_type }}</dd></div>
                        <div>
                            <dt class="{{ $label }}">Priority</dt>
                            <dd class="mt-1"><span class="inline-flex rounded-md px-2 py-0.5 text-xs font-medium ring-1 ring-inset {{ $priority[$case->priority] ?? '' }}">{{ $case->priority }}</span></dd>
                        </div>
                        <div><dt class="{{ $label }}">Surgery Date</dt><dd class="{{ $value }}">{{ $case->surgery_date?->format('d M Y') ?? '—' }}</dd></div>
                    </dl>
                </div>

                <div class="border-t border-gray-200 bg-gray-50 p-6 sm:px-8">
                    <h3 class="text-xs font-semibold uppercase tracking-wider text-gray-500">Record history</h3>
                    <dl class="mt-3 grid grid-cols-1 gap-x-8 gap-y-3 text-sm sm:grid-cols-3">
                        <div><dt class="text-gray-500">Created by</dt><dd class="font-medium text-gray-900">{{ $case->creator?->name ?? 'Unknown' }}</dd></div>
                        <div><dt class="text-gray-500">Created at</dt><dd class="font-medium text-gray-900">{{ $case->created_at->format('d M Y, H:i') }}</dd></div>
                        <div><dt class="text-gray-500">Last updated</dt><dd class="font-medium text-gray-900">{{ $case->updated_at->format('d M Y, H:i') }}</dd></div>
                    </dl>
                </div>

                @canany(['update', 'delete'], $case)
                    <div class="flex items-center justify-end gap-3 rounded-b-xl border-t border-gray-200 px-6 py-4 sm:px-8">
                        @can('delete', $case)
                            <form method="POST" action="{{ route('cases.destroy', $case) }}"
                                  onsubmit="return confirm('Delete {{ $case->case_number }}?')">
                                @csrf
                                @method('DELETE')
                                <button class="rounded-lg px-4 py-2 text-xs font-semibold uppercase tracking-widest text-red-700 ring-1 ring-inset ring-red-200 hover:bg-red-50">Delete</button>
                            </form>
                        @endcan
                        @can('update', $case)
                            <a href="{{ route('cases.edit', $case) }}"
                               class="rounded-lg bg-indigo-600 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white shadow-sm hover:bg-indigo-500">Edit case</a>
                        @endcan
                    </div>
                @endcanany
            </div>
        </div>
    </div>
</x-app-layout>
