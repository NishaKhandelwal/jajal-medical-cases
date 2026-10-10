<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h2 class="font-bold text-2xl text-gray-900 tracking-tight">{{ __('New case') }}</h2>
                <p class="mt-1 text-sm text-gray-500">Fill in the details below to register a new implant case.</p>
            </div>
            <a href="{{ route('cases.index') }}" class="text-sm font-medium text-gray-500 hover:text-gray-800">← Back to list</a>
        </div>
    </x-slot>
    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-200">
                @include('cases._form', ['case' => $case, 'action' => route('cases.store'), 'method' => 'POST'])
            </div>
        </div>
    </div>
</x-app-layout>
