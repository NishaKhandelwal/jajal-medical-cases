<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Edit') }} {{ $case->case_number }}</h2>
    </x-slot>
    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                @include('cases._form', ['case' => $case, 'action' => route('cases.update', $case), 'method' => 'PUT'])
            </div>
        </div>
    </div>
</x-app-layout>
