<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $case->case_number }}</h2>
            <a href="{{ route('cases.index') }}" class="text-sm text-gray-600 underline">Back to list</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if (session('success'))
                <div class="mx-4 sm:mx-0 p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4 text-sm">
                    <div><dt class="text-gray-500">ID</dt><dd class="font-medium">{{ $case->id }}</dd></div>
                    <div><dt class="text-gray-500">Case Number</dt><dd class="font-medium">{{ $case->case_number }}</dd></div>
                    <div><dt class="text-gray-500">Patient Reference</dt><dd class="font-medium">{{ $case->patient_reference }}</dd></div>
                    <div><dt class="text-gray-500">Surgeon</dt><dd class="font-medium">{{ $case->surgeon_name }}</dd></div>
                    <div><dt class="text-gray-500">Implant Type</dt><dd class="font-medium">{{ $case->implant_type }}</dd></div>
                    <div><dt class="text-gray-500">Status</dt><dd class="font-medium">{{ $case->status }}</dd></div>
                    <div><dt class="text-gray-500">Priority</dt><dd class="font-medium">{{ $case->priority }}</dd></div>
                    <div><dt class="text-gray-500">Surgery Date</dt><dd class="font-medium">{{ $case->surgery_date?->format('d M Y') ?? '—' }}</dd></div>
                    <div><dt class="text-gray-500">Created By</dt><dd class="font-medium">{{ $case->creator?->name ?? 'Unknown' }}</dd></div>
                    <div><dt class="text-gray-500">Created At</dt><dd class="font-medium">{{ $case->created_at->format('d M Y, H:i') }}</dd></div>
                    <div><dt class="text-gray-500">Updated At</dt><dd class="font-medium">{{ $case->updated_at->format('d M Y, H:i') }}</dd></div>
                </dl>

                <div class="mt-6 flex gap-4">
                    @can('update', $case)
                        <a href="{{ route('cases.edit', $case) }}" class="text-yellow-600 hover:underline">Edit</a>
                    @endcan
                    @can('delete', $case)
                        <form method="POST" action="{{ route('cases.destroy', $case) }}"
                              onsubmit="return confirm('Delete {{ $case->case_number }}?')">
                            @csrf
                            @method('DELETE')
                            <button class="text-red-600 hover:underline">Delete</button>
                        </form>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
