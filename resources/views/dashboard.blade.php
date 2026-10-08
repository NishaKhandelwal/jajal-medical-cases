<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Dashboard') }}</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4 px-4 sm:px-0">
                @foreach ($stats as $stat)
                    <a href="{{ $stat['status'] ? route('cases.index', ['status' => $stat['status']]) : route('cases.index') }}"
                       class="block bg-white shadow-sm sm:rounded-lg p-5 hover:bg-gray-50">
                        <div class="text-sm text-gray-500">{{ $stat['label'] }}</div>
                        <div class="mt-1 text-3xl font-bold text-gray-900">{{ $stat['count'] }}</div>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>
