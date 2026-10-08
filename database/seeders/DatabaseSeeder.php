<?php

namespace Database\Seeders;

use App\Models\MedicalCase;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Passwords come from .env (see .env.example) so they are not hard-coded here.
        $adminPassword = env('SEED_ADMIN_PASSWORD');
        $viewerPassword = env('SEED_VIEWER_PASSWORD');

        if (! $adminPassword || ! $viewerPassword) {
            $this->command->error('Set SEED_ADMIN_PASSWORD and SEED_VIEWER_PASSWORD in .env first.');

            return;
        }

        $admin = User::factory()->create([
            'name' => 'Demo Admin',
            'email' => env('SEED_ADMIN_EMAIL', 'admin@example.com'),
            'password' => Hash::make($adminPassword),
            'role' => User::ROLE_ADMIN,
        ]);

        User::factory()->create([
            'name' => 'Demo Viewer',
            'email' => env('SEED_VIEWER_EMAIL', 'viewer@example.com'),
            'password' => Hash::make($viewerPassword),
            'role' => User::ROLE_VIEWER,
        ]);

        // 25 dummy cases with predictable numbers CASE-0001 ... CASE-0025
        MedicalCase::factory()
            ->count(25)
            ->sequence(fn ($seq) => ['case_number' => sprintf('CASE-%04d', $seq->index + 1)])
            ->create(['created_by' => $admin->id]);
    }
}
