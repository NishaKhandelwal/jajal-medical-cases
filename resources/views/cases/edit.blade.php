<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h2 class="font-bold text-2xl text-gray-900 tracking-tight">{{ __('Edit') }} {{ $case->case_number }}</h2>
                <p class="mt-1 text-sm text-gray-500">Update the case details or change its status.</p>
            </div>
            <a href="{{ route('cases.show', $case) }}" class="text-sm font-medium text-gray-500 hover:text-gray-800">← Back to case</a>
        </div>
    </x-slot>
    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-200">
                @include('cases._form', ['case' => $case, 'action' => route('cases.update', $case), 'method' => 'PUT'])
            </div>
        </div>
    </div>
</x-app-layout>
