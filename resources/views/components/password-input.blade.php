@props(['disabled' => false])

<div x-data="{ show: false }" class="relative">
    <input type="password" :type="show ? 'text' : 'password'" @disabled($disabled)
        {{ $attributes->merge(['class' => 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm pe-16']) }}>

    <button type="button" @click="show = ! show"
        :aria-label="show ? 'Hide password' : 'Show password'"
        class="absolute inset-y-0 end-0 flex items-center px-3 text-xs font-semibold uppercase tracking-wider text-gray-500 hover:text-gray-800 focus:outline-none focus:text-indigo-600">
        <span x-text="show ? 'Hide' : 'Show'">Show</span>
    </button>
</div>