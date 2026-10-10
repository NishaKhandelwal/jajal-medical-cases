@php
    $sortLink = function (string $col) {
        $dir = request('sort') === $col && request('direction') === 'asc' ? 'desc' : 'asc';
        return route('cases.index', array_merge(request()->only('search', 'status', 'priority'), ['sort' => $col, 'direction' => $dir]));
    };
    $sortIcon = function (string $col) {
        if (request('sort') !== $col) return '↕';
        return request('direction') === 'asc' ? '↑' : '↓';
    };
    // [badge classes, dot colour]
    $status = [
        'Draft'     => ['bg-gray-50 text-gray-700 ring-gray-200',        'bg-gray-400'],
        'Planning'  => ['bg-blue-50 text-blue-700 ring-blue-200',        'bg-blue-500'],
        'In Review' => ['bg-amber-50 text-amber-700 ring-amber-200',     'bg-amber-500'],
        'Approved'  => ['bg-green-50 text-green-700 ring-green-200',     'bg-green-500'],
        'Completed' => ['bg-emerald-50 text-emerald-700 ring-emerald-200','bg-emerald-600'],
        'Cancelled' => ['bg-red-50 text-red-700 ring-red-200',           'bg-red-500'],
    ];
    $priority = [
        'Low'    => 'bg-slate-50 text-slate-600 ring-slate-200',
        'Medium' => 'bg-amber-50 text-amber-700 ring-amber-200',
        'High'   => 'bg-red-50 text-red-700 ring-red-200',
    ];
    $field = 'block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500';
    $th = 'px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500';
@endphp
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h2 class="font-bold text-2xl text-gray-900 tracking-tight">{{ __('Cases') }}</h2>
                <p class="mt-1 text-sm text-gray-500">Search, filter and manage implant cases.</p>
            </div>
            @can('create', App\Models\MedicalCase::class)
                <a href="{{ route('cases.create') }}"
                   class="inline-flex items-center gap-1.5 rounded-lg bg-indigo-600 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white shadow-sm hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    <span class="text-base leading-none">+</span> New case
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
            @if (session('success'))
                <div class="flex items-center gap-2 rounded-lg bg-green-50 px-4 py-3 text-sm text-green-800 ring-1 ring-green-200">
                    <span class="font-bold">✓</span> {{ session('success') }}
                </div>
            @endif

            {{-- Filters --}}
            <form method="GET" action="{{ route('cases.index') }}"
                  class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-200 grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
                <div class="md:col-span-5">
                    <label for="search" class="mb-1 block text-xs font-semibold uppercase tracking-wider text-gray-500">Search</label>
                    <input id="search" type="text" name="search" value="{{ request('search') }}" placeholder="Case number or surgeon"
                           class="{{ $field }}">
                </div>
                <div class="md:col-span-2">
                    <label for="status" class="mb-1 block text-xs font-semibold uppercase tracking-wider text-gray-500">Status</label>
                    <select id="status" name="status" class="{{ $field }}">
                        <option value="">All statuses</option>
                        @foreach ($statuses as $s)
                            <option value="{{ $s }}" @selected(request('status') === $s)>{{ $s }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label for="priority" class="mb-1 block text-xs font-semibold uppercase tracking-wider text-gray-500">Priority</label>
                    <select id="priority" name="priority" class="{{ $field }}">
                        <option value="">All priorities</option>
                        @foreach ($priorities as $p)
                            <option value="{{ $p }}" @selected(request('priority') === $p)>{{ $p }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-3 flex items-center gap-3">
                    <x-primary-button>Filter</x-primary-button>
                    <a href="{{ route('cases.index') }}" class="text-sm font-medium text-gray-500 hover:text-gray-800">Reset</a>
                </div>
            </form>

            {{-- Table --}}
            <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-200">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50 text-left">
                            <tr>
                                <th class="{{ $th }}"><a href="{{ $sortLink('case_number') }}" class="hover:text-indigo-600">Case No. {{ $sortIcon('case_number') }}</a></th>
                                <th class="{{ $th }}"><a href="{{ $sortLink('surgeon_name') }}" class="hover:text-indigo-600">Surgeon {{ $sortIcon('surgeon_name') }}</a></th>
                                <th class="{{ $th }}">Implant</th>
                                <th class="{{ $th }}"><a href="{{ $sortLink('status') }}" class="hover:text-indigo-600">Status {{ $sortIcon('status') }}</a></th>
                                <th class="{{ $th }}"><a href="{{ $sortLink('priority') }}" class="hover:text-indigo-600">Priority {{ $sortIcon('priority') }}</a></th>
                                <th class="{{ $th }}"><a href="{{ $sortLink('surgery_date') }}" class="hover:text-indigo-600">Surgery {{ $sortIcon('surgery_date') }}</a></th>
                                <th class="{{ $th }} text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($cases as $case)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-4 py-3.5 whitespace-nowrap">
                                        <a href="{{ route('cases.show', $case) }}" class="font-semibold text-indigo-600 hover:text-indigo-800">{{ $case->case_number }}</a>
                                    </td>
                                    <td class="px-4 py-3.5 whitespace-nowrap text-gray-900">{{ $case->surgeon_name }}</td>
                                    <td class="px-4 py-3.5 whitespace-nowrap text-gray-600">{{ $case->implant_type }}</td>
                                    <td class="px-4 py-3.5 whitespace-nowrap">
                                        @php [$sb, $sd] = $status[$case->status] ?? $status['Draft']; @endphp
                                        <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium ring-1 ring-inset {{ $sb }}">
                                            <span class="h-1.5 w-1.5 rounded-full {{ $sd }}"></span>{{ $case->status }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3.5 whitespace-nowrap">
                                        <span class="inline-flex rounded-md px-2 py-0.5 text-xs font-medium ring-1 ring-inset {{ $priority[$case->priority] ?? '' }}">{{ $case->priority }}</span>
                                    </td>
                                    <td class="px-4 py-3.5 whitespace-nowrap text-gray-600">{{ $case->surgery_date?->format('d M Y') ?? '—' }}</td>
                                    <td class="px-4 py-3.5 whitespace-nowrap">
                                        <div class="flex items-center justify-end gap-2">
                                            @can('view', $case)
                                                <a href="{{ route('cases.show', $case) }}"
                                                   class="rounded-md px-2.5 py-1 text-xs font-medium text-indigo-700 ring-1 ring-inset ring-indigo-200 hover:bg-indigo-50">View</a>
                                            @endcan
                                            @can('update', $case)
                                                <a href="{{ route('cases.edit', $case) }}"
                                                   class="rounded-md px-2.5 py-1 text-xs font-medium text-gray-700 ring-1 ring-inset ring-gray-300 hover:bg-gray-50">Edit</a>
                                            @endcan
                                            @can('delete', $case)
                                                <form method="POST" action="{{ route('cases.destroy', $case) }}" class="inline"
                                                      onsubmit="return confirm('Delete {{ $case->case_number }}?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="rounded-md px-2.5 py-1 text-xs font-medium text-red-700 ring-1 ring-inset ring-red-200 hover:bg-red-50">Delete</button>
                                                </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-14 text-center">
                                        <div class="text-base font-semibold text-gray-700">No cases found</div>
                                        <p class="mt-1 text-sm text-gray-500">Try a different search or reset the filters.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($cases->total() > 0)
                    <div class="flex flex-col gap-3 border-t border-gray-200 bg-gray-50 px-4 py-3 sm:flex-row sm:items-center sm:justify-between">
                        <p class="text-sm text-gray-600">
                            Showing <span class="font-semibold">{{ $cases->firstItem() }}</span>–<span class="font-semibold">{{ $cases->lastItem() }}</span>
                            of <span class="font-semibold">{{ $cases->total() }}</span> cases
                        </p>
                        <div>{{ $cases->onEachSide(1)->links() }}</div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
