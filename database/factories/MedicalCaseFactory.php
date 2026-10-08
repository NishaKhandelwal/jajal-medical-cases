<?php

namespace Database\Factories;

use App\Models\MedicalCase;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MedicalCaseFactory extends Factory
{
    protected $model = MedicalCase::class;

    public function definition(): array
    {
        return [
            'case_number' => 'CASE-' . fake()->unique()->numberBetween(1000, 99999),
            'patient_reference' => strtoupper(fake()->bothify('PT-####??')), // dummy code, not a name
            'surgeon_name' => 'Dr. ' . fake()->lastName(),
            'implant_type' => fake()->randomElement([
                'Knee Implant', 'Hip Implant', 'Spinal Cage', 'Shoulder Implant', 'Dental Implant',
            ]),
            'status' => fake()->randomElement(MedicalCase::STATUSES),
            'priority' => fake()->randomElement(MedicalCase::PRIORITIES),
            'surgery_date' => fake()->optional(0.7)->dateTimeBetween('-1 month', '+3 months'),
            'created_by' => User::factory(),
        ];
    }
}
