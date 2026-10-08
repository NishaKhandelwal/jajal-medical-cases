<?php

namespace Tests\Feature;

use App\Models\MedicalCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CaseFeatureTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    private function viewer(): User
    {
        return User::factory()->create(['role' => 'viewer']);
    }

    private function payload(array $override = []): array
    {
        return array_merge([
            'case_number' => 'CASE-9001',
            'patient_reference' => 'PT-TEST01',
            'surgeon_name' => 'Dr. Test',
            'implant_type' => 'Knee Implant',
            'status' => 'Draft',
            'priority' => 'Medium',
            'surgery_date' => '2026-12-01',
        ], $override);
    }

    // 1
    public function test_valid_login_succeeds(): void
    {
        $user = $this->admin(); // factory default password is "password"

        $response = $this->post('/login', ['email' => $user->email, 'password' => 'password']);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect('/dashboard');
    }

    // 2
    public function test_invalid_login_is_rejected(): void
    {
        $user = $this->admin();

        $response = $this->from('/login')->post('/login', ['email' => $user->email, 'password' => 'wrong-password']);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    // 3
    public function test_guest_cannot_open_case_pages_or_apis(): void
    {
        $this->get('/cases')->assertRedirect('/login');
        $this->get('/dashboard')->assertRedirect('/login');
        $this->getJson('/api/cases')->assertStatus(401)->assertJsonPath('success', false);
    }

    // 4
    public function test_admin_can_create_a_case(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->post('/cases', $this->payload())
            ->assertRedirect(route('cases.index'));

        $this->assertDatabaseHas('cases', ['case_number' => 'CASE-9001', 'created_by' => $admin->id]);
    }

    public function test_admin_can_create_a_case_via_api(): void
    {
        Sanctum::actingAs($this->admin());

        $this->postJson('/api/cases', $this->payload())
            ->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.case_number', 'CASE-9001');
    }

    // 5
    public function test_duplicate_case_number_fails_with_422(): void
    {
        Sanctum::actingAs($this->admin());
        MedicalCase::factory()->create(['case_number' => 'CASE-9001']);

        $this->postJson('/api/cases', $this->payload())
            ->assertStatus(422)
            ->assertJsonPath('success', false)
            ->assertJsonValidationErrors('case_number')
            ->assertJsonStructure(['errors' => ['case_number']]);
    }

    public function test_update_may_keep_its_own_case_number(): void
    {
        Sanctum::actingAs($this->admin());
        $case = MedicalCase::factory()->create(['case_number' => 'CASE-9001']);

        $this->putJson("/api/cases/{$case->id}", $this->payload(['status' => 'Approved']))
            ->assertOk()
            ->assertJsonPath('data.status', 'Approved');
    }

    // 6
    public function test_viewer_cannot_delete_a_case(): void
    {
        $case = MedicalCase::factory()->create();

        $this->actingAs($this->viewer())->delete("/cases/{$case->id}")->assertForbidden();

        Sanctum::actingAs($this->viewer());
        $this->deleteJson("/api/cases/{$case->id}")->assertStatus(403);

        $this->assertDatabaseHas('cases', ['id' => $case->id]);
    }

    public function test_viewer_cannot_create_even_with_invalid_data(): void
    {
        Sanctum::actingAs($this->viewer());

        $this->postJson('/api/cases', [])->assertStatus(403); // 403, not 422
    }
}
