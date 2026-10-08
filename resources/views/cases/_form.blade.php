{{-- Reusable for create AND edit. Expects: $case, $action, $method, $statuses, $priorities --}}
@php $select = 'mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm'; @endphp

<form method="POST" action="{{ $action }}" class="space-y-5">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <div>
        <x-input-label for="case_number" value="Case Number" />
        <x-text-input id="case_number" name="case_number" type="text" class="mt-1 block w-full"
                      :value="old('case_number', $case->case_number)" required />
        <x-input-error :messages="$errors->get('case_number')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="patient_reference" value="Patient Reference (dummy code, not a real name)" />
        <x-text-input id="patient_reference" name="patient_reference" type="text" class="mt-1 block w-full"
                      :value="old('patient_reference', $case->patient_reference)" required />
        <x-input-error :messages="$errors->get('patient_reference')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="surgeon_name" value="Surgeon Name" />
        <x-text-input id="surgeon_name" name="surgeon_name" type="text" class="mt-1 block w-full"
                      :value="old('surgeon_name', $case->surgeon_name)" required />
        <x-input-error :messages="$errors->get('surgeon_name')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="implant_type" value="Implant Type" />
        <x-text-input id="implant_type" name="implant_type" type="text" class="mt-1 block w-full"
                      :value="old('implant_type', $case->implant_type)" required />
        <x-input-error :messages="$errors->get('implant_type')" class="mt-2" />
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
        <div>
            <x-input-label for="status" value="Status" />
            <select id="status" name="status" class="{{ $select }}" required>
                @foreach ($statuses as $s)
                    <option value="{{ $s }}" @selected(old('status', $case->status) === $s)>{{ $s }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('status')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="priority" value="Priority" />
            <select id="priority" name="priority" class="{{ $select }}" required>
                @foreach ($priorities as $p)
                    <option value="{{ $p }}" @selected(old('priority', $case->priority) === $p)>{{ $p }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('priority')" class="mt-2" />
        </div>
    </div>

    <div>
        <x-input-label for="surgery_date" value="Surgery Date (optional)" />
        <x-text-input id="surgery_date" name="surgery_date" type="date" class="mt-1 block w-full"
                      :value="old('surgery_date', $case->surgery_date?->format('Y-m-d'))" />
        <x-input-error :messages="$errors->get('surgery_date')" class="mt-2" />
    </div>

    <div class="flex items-center gap-4">
        <x-primary-button>Save</x-primary-button>
        <a href="{{ route('cases.index') }}" class="text-sm text-gray-600 underline">Cancel</a>
    </div>
</form>
