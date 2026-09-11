<?php

namespace Tests\Feature;

use App\Models\Candidate;
use App\Models\Constituency;
use App\Models\County;
use App\Models\ElectionType;
use App\Models\PollingStation;
use App\Models\PresidingOfficer;
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

    public function test_admin_can_add_an_officer_and_agents_can_only_select_active_officers(): void
    {
        $superAdmin = User::factory()->create(['role' => 'super_admin', 'is_active' => true]);
        $agent = User::factory()->create(['role' => 'agent', 'is_active' => true]);
        $county = County::create(['name' => 'Kakamega', 'code' => '037']);
        $constituency = Constituency::create(['county_id' => $county->id, 'name' => 'Lurambi']);
        $ward = Ward::create(['constituency_id' => $constituency->id, 'name' => 'Sheywe']);
        $station = PollingStation::create(['ward_id' => $ward->id, 'name' => 'Admin Station', 'registered_voters' => 500]);
        $electionType = ElectionType::create(['name' => 'Governor']);

        $this->actingAs($superAdmin)
            ->post(route('manage.presidingOfficers.store'), [
                'name' => 'Jane Officer',
                'code' => 'PO-001',
            ])
            ->assertRedirect();

        $officer = PresidingOfficer::firstOrFail();

        $this->actingAs($agent)
            ->get(route('votes.create'))
            ->assertOk()
            ->assertSee('Jane Officer')
            ->assertDontSee('name="presiding_officer"');

        $this->actingAs($agent)
            ->post(route('votes.store'), [
                'election_type_id' => $electionType->id,
                'polling_station_id' => $station->id,
                'presiding_officer' => 'Manually Entered Officer',
                'agent_name' => $agent->name,
                'agent_code' => 'AGT123',
                'candidate_votes' => [],
                'spoilt_votes' => 0,
                'total_votes_cast' => 0,
                'registered_voters' => 500,
            ])
            ->assertSessionHasErrors('presiding_officer_id');

        $candidate = Candidate::create([
            'election_type_id' => $electionType->id,
            'name' => 'Test Candidate',
            'party' => 'TEST',
        ]);

        $this->actingAs($agent)
            ->post(route('votes.store'), [
                'election_type_id' => $electionType->id,
                'polling_station_id' => $station->id,
                'presiding_officer_id' => $officer->id,
                'agent_name' => $agent->name,
                'agent_code' => 'AGT123',
                'candidate_votes' => [
                    ['candidate_id' => $candidate->id, 'votes' => 10],
                ],
                'spoilt_votes' => 1,
                'total_votes_cast' => 11,
                'registered_voters' => 500,
            ])
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('vote_submissions', [
            'polling_station_id' => $station->id,
            'presiding_officer_id' => $officer->id,
            'presiding_officer' => 'Jane Officer',
        ]);

        $this->actingAs($superAdmin)
            ->delete(route('manage.presidingOfficers.destroy', $officer))
            ->assertRedirect();

        $this->assertDatabaseHas('presiding_officers', [
            'id' => $officer->id,
            'is_active' => false,
        ]);
    }
}
