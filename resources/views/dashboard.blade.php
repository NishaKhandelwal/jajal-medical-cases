@php
    $total = collect($stats)->firstWhere('status', null)['count'] ?? 0;
    $accent = [
        'Total cases' => ['border-indigo-500',  'bg-indigo-500',  'text-indigo-600'],
        'Planning'    => ['border-blue-500',    'bg-blue-500',    'text-blue-600'],
        'In Review'   => ['border-amber-500',   'bg-amber-500',   'text-amber-600'],
        'Approved'    => ['border-green-500',   'bg-green-500',   'text-green-600'],
        'Completed'   => ['border-emerald-600', 'bg-emerald-600', 'text-emerald-700'],
    ];
@endphp
<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="font-bold text-2xl text-gray-900 tracking-tight">{{ __('Dashboard') }}</h2>
                <p class="mt-1 text-sm text-gray-500">Welcome back, {{ Auth::user()->name }}. Here is an overview of all implant cases.</p>
            </div>
            <a href="{{ route('cases.index') }}"
               class="inline-flex items-center justify-center rounded-lg bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm ring-1 ring-gray-300 hover:bg-gray-50">
                View all cases →
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                @foreach ($stats as $stat)
                    @php
                        [$border, $bar, $text] = $accent[$stat['label']] ?? $accent['Total cases'];
                        $pct = $total > 0 ? round($stat['count'] / $total * 100) : 0;
                    @endphp
                    <a href="{{ $stat['status'] ? route('cases.index', ['status' => $stat['status']]) : route('cases.index') }}"
                       class="group block rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-200 border-t-4 {{ $border }} transition hover:-translate-y-0.5 hover:shadow-md">
                        <div class="text-xs font-semibold uppercase tracking-wider text-gray-500">{{ $stat['label'] }}</div>
                        <div class="mt-2 text-4xl font-bold {{ $text }}">{{ $stat['count'] }}</div>
                        @if ($stat['status'])
                            <div class="mt-4 h-1.5 w-full rounded-full bg-gray-100">
                                <div class="h-1.5 rounded-full {{ $bar }}" style="width: {{ $pct }}%"></div>
                            </div>
                            <div class="mt-1.5 text-xs text-gray-500">{{ $pct }}% of all cases</div>
                        @else
                            <div class="mt-4 text-xs text-gray-500 group-hover:text-indigo-600">Click to browse the list</div>
                        @endif
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>
