{{-- Reusable for create AND edit. Expects: $case, $action, $method, $statuses, $priorities --}}
@php $select = 'mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500'; @endphp

<form method="POST" action="{{ $action }}">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    {{-- Section: case details --}}
    <div class="p-6 sm:p-8 space-y-6">
        <div>
            <h3 class="text-base font-semibold text-gray-900">Case details</h3>
            <p class="mt-1 text-sm text-gray-500">Identify the case and the people involved. Use dummy data only.</p>
        </div>

        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
            <div>
                <x-input-label for="case_number" value="Case Number" />
                <x-text-input id="case_number" name="case_number" type="text" class="mt-1 block w-full"
                              :value="old('case_number', $case->case_number)" required />
                <x-input-error :messages="$errors->get('case_number')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="patient_reference" value="Patient Reference" />
                <x-text-input id="patient_reference" name="patient_reference" type="text" class="mt-1 block w-full"
                              placeholder="e.g. PT-0001AB" :value="old('patient_reference', $case->patient_reference)" required />
                <p class="mt-1 text-xs text-gray-500">A dummy code, not a real patient name.</p>
                <x-input-error :messages="$errors->get('patient_reference')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="surgeon_name" value="Surgeon Name" />
                <x-text-input id="surgeon_name" name="surgeon_name" type="text" class="mt-1 block w-full"
                              placeholder="e.g. Dr. Rao" :value="old('surgeon_name', $case->surgeon_name)" required />
                <x-input-error :messages="$errors->get('surgeon_name')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="implant_type" value="Implant Type" />
                <x-text-input id="implant_type" name="implant_type" type="text" class="mt-1 block w-full"
                              placeholder="e.g. Knee Implant" :value="old('implant_type', $case->implant_type)" required />
                <x-input-error :messages="$errors->get('implant_type')" class="mt-2" />
            </div>
        </div>
    </div>

    {{-- Section: workflow --}}
    <div class="border-t border-gray-200 p-6 sm:p-8 space-y-6">
        <div>
            <h3 class="text-base font-semibold text-gray-900">Workflow</h3>
            <p class="mt-1 text-sm text-gray-500">Where the case is in the process and when surgery is planned.</p>
        </div>

        <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
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

            <div>
                <x-input-label for="surgery_date" value="Surgery Date (optional)" />
                <x-text-input id="surgery_date" name="surgery_date" type="date" class="mt-1 block w-full"
                              :value="old('surgery_date', $case->surgery_date?->format('Y-m-d'))" />
                <x-input-error :messages="$errors->get('surgery_date')" class="mt-2" />
            </div>
        </div>
    </div>

    {{-- Footer --}}
    <div class="flex items-center justify-end gap-4 rounded-b-xl border-t border-gray-200 bg-gray-50 px-6 py-4 sm:px-8">
        <a href="{{ route('cases.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900">Cancel</a>
        <x-primary-button>Save case</x-primary-button>
    </div>
</form>
