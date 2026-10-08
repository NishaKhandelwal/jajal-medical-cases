@php
    $sortLink = function (string $col) {
        $dir = request('sort') === $col && request('direction') === 'asc' ? 'desc' : 'asc';
        return route('cases.index', array_merge(request()->only('search', 'status', 'priority'), ['sort' => $col, 'direction' => $dir]));
    };
    $badge = [
        'Draft' => 'bg-gray-100 text-gray-800', 'Planning' => 'bg-blue-100 text-blue-800',
        'In Review' => 'bg-yellow-100 text-yellow-800', 'Approved' => 'bg-green-100 text-green-800',
        'Completed' => 'bg-emerald-100 text-emerald-800', 'Cancelled' => 'bg-red-100 text-red-800',
    ];
    $field = 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm';
@endphp
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Cases') }}</h2>
            @can('create', App\Models\MedicalCase::class)
                <a href="{{ route('cases.create') }}"
                   class="px-4 py-2 bg-gray-800 text-white text-xs font-semibold uppercase rounded-md hover:bg-gray-700">New case</a>
            @endcan
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if (session('success'))
                <div class="mx-4 sm:mx-0 p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
            @endif

            <form method="GET" action="{{ route('cases.index') }}"
                  class="bg-white p-4 shadow-sm sm:rounded-lg grid grid-cols-1 md:grid-cols-5 gap-3">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Case number or surgeon"
                       class="md:col-span-2 {{ $field }}">
                <select name="status" class="{{ $field }}">
                    <option value="">All statuses</option>
                    @foreach ($statuses as $s)
                        <option value="{{ $s }}" @selected(request('status') === $s)>{{ $s }}</option>
                    @endforeach
                </select>
                <select name="priority" class="{{ $field }}">
                    <option value="">All priorities</option>
                    @foreach ($priorities as $p)
                        <option value="{{ $p }}" @selected(request('priority') === $p)>{{ $p }}</option>
                    @endforeach
                </select>
                <div class="flex gap-2">
                    <x-primary-button>Filter</x-primary-button>
                    <a href="{{ route('cases.index') }}" class="px-4 py-2 text-sm text-gray-600 underline self-center">Reset</a>
                </div>
            </form>

            <div class="bg-white shadow-sm sm:rounded-lg overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-left text-gray-600">
                        <tr>
                            <th class="px-4 py-3"><a href="{{ $sortLink('case_number') }}">Case No. ↕</a></th>
                            <th class="px-4 py-3"><a href="{{ $sortLink('surgeon_name') }}">Surgeon ↕</a></th>
                            <th class="px-4 py-3">Implant</th>
                            <th class="px-4 py-3"><a href="{{ $sortLink('status') }}">Status ↕</a></th>
                            <th class="px-4 py-3"><a href="{{ $sortLink('priority') }}">Priority ↕</a></th>
                            <th class="px-4 py-3"><a href="{{ $sortLink('surgery_date') }}">Surgery ↕</a></th>
                            <th class="px-4 py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($cases as $case)
                            <tr>
                                <td class="px-4 py-3 font-medium">{{ $case->case_number }}</td>
                                <td class="px-4 py-3">{{ $case->surgeon_name }}</td>
                                <td class="px-4 py-3">{{ $case->implant_type }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 rounded text-xs {{ $badge[$case->status] ?? '' }}">{{ $case->status }}</span>
                                </td>
                                <td class="px-4 py-3">{{ $case->priority }}</td>
                                <td class="px-4 py-3">{{ $case->surgery_date?->format('d M Y') ?? '—' }}</td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    @can('view', $case)
                                        <a href="{{ route('cases.show', $case) }}" class="text-indigo-600 hover:underline">View</a>
                                    @endcan
                                    @can('update', $case)
                                        <a href="{{ route('cases.edit', $case) }}" class="ml-3 text-yellow-600 hover:underline">Edit</a>
                                    @endcan
                                    @can('delete', $case)
                                        <form method="POST" action="{{ route('cases.destroy', $case) }}" class="inline"
                                              onsubmit="return confirm('Delete {{ $case->case_number }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="ml-3 text-red-600 hover:underline">Delete</button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="px-4 py-6 text-center text-gray-500">No cases found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-4 sm:px-0">{{ $cases->links() }}</div>
        </div>
    </div>
</x-app-layout>
