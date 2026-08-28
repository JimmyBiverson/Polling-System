<?php

namespace Tests\Feature;

use App\Models\Constituency;
use App\Models\County;
use App\Models\ElectionType;
use App\Models\PollingStation;
use App\Models\User;
use App\Models\VoteSubmission;
use App\Models\Ward;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuperAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_toggle_user_status_and_override_submission(): void
    {
        $superAdmin = User::factory()->create(['role' => 'super_admin', 'is_active' => true]);
        $agent = User::factory()->create(['role' => 'agent', 'is_active' => true]);

        // Toggle user status
        $response = $this->actingAs($superAdmin)
            ->post(route('manage.users.toggleStatus', $agent));

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'id' => $agent->id,
            'is_active' => false,
        ]);

        // Test status override
        $county = County::create(['name' => 'Kakamega', 'code' => '037']);
        $constituency = Constituency::create(['county_id' => $county->id, 'name' => 'Lurambi']);
        $ward = Ward::create(['constituency_id' => $constituency->id, 'name' => 'Sheywe']);
        $station = PollingStation::create(['ward_id' => $ward->id, 'name' => 'Test Station', 'registered_voters' => 500]);
        $electionType = ElectionType::create(['name' => 'Governor Test']);

        $submission = VoteSubmission::create([
            'polling_station_id' => $station->id,
            'election_type_id' => $electionType->id,
            'user_id' => $agent->id,
            'agent_name' => $agent->name,
            'agent_code' => 'AGT123',
            'status' => 'pending',
            'spoilt_votes' => 2,
            'total_votes_cast' => 100,
            'submitted_at' => now(),
        ]);

        $overrideResponse = $this->actingAs($superAdmin)
            ->post(route('votes.override', $submission), [
                'status' => 'verified',
                'notes' => 'Super Admin force verified Form 34A after audit check',
            ]);

        $overrideResponse->assertRedirect();
        $this->assertDatabaseHas('vote_submissions', [
            'id' => $submission->id,
            'status' => 'verified',
        ]);
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'super_admin_override',
        ]);
    }
}
