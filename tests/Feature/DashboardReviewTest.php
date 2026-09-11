<?php

namespace Tests\Feature;

use App\Models\Candidate;
use App\Models\Constituency;
use App\Models\County;
use App\Models\ElectionType;
use App\Models\PollingStation;
use App\Models\PresidingOfficer;
use App\Models\User;
use App\Models\VoteDetail;
use App\Models\VoteSubmission;
use App\Models\Ward;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardReviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_filter_pending_results_by_election_category_and_source(): void
    {
        $countyAdmin = User::factory()->create(['role' => 'county_admin', 'is_active' => true]);
        $agent = User::factory()->create(['role' => 'agent', 'is_active' => true]);
        $county = County::create(['name' => 'Kakamega', 'code' => '037']);
        $constituency = Constituency::create(['county_id' => $county->id, 'name' => 'Lurambi']);
        $ward = Ward::create(['constituency_id' => $constituency->id, 'name' => 'Sheywe']);
        $station = PollingStation::create(['ward_id' => $ward->id, 'name' => 'Review Station', 'registered_voters' => 500]);
        $presidingOfficer = PresidingOfficer::create(['name' => 'Review Officer']);
        $presidential = ElectionType::create(['name' => 'Presidential', 'is_active' => true]);
        $governor = ElectionType::create(['name' => 'Governor', 'is_active' => true]);
        $presidentialCandidate = Candidate::create([
            'election_type_id' => $presidential->id,
            'name' => 'Presidential Candidate',
            'party' => 'TEST',
        ]);
        $governorCandidate = Candidate::create([
            'election_type_id' => $governor->id,
            'name' => 'Governor Candidate',
            'party' => 'TEST',
        ]);

        $pending = VoteSubmission::create([
            'polling_station_id' => $station->id,
            'election_type_id' => $presidential->id,
            'presiding_officer_id' => $presidingOfficer->id,
            'user_id' => $agent->id,
            'agent_name' => $agent->name,
            'agent_code' => 'AGENT-001',
            'presiding_officer' => $presidingOfficer->name,
            'spoilt_votes' => 1,
            'total_votes_cast' => 11,
            'registered_voters' => 500,
            'status' => 'pending',
            'submitted_at' => now(),
        ]);
        VoteDetail::create(['vote_submission_id' => $pending->id, 'candidate_id' => $presidentialCandidate->id, 'votes' => 10]);

        $verified = VoteSubmission::create([
            'polling_station_id' => $station->id,
            'election_type_id' => $governor->id,
            'presiding_officer_id' => $presidingOfficer->id,
            'user_id' => $agent->id,
            'agent_name' => $agent->name,
            'agent_code' => 'AGENT-002',
            'presiding_officer' => $presidingOfficer->name,
            'spoilt_votes' => 0,
            'total_votes_cast' => 8,
            'registered_voters' => 500,
            'status' => 'verified',
            'submitted_at' => now()->subMinute(),
        ]);
        VoteDetail::create(['vote_submission_id' => $verified->id, 'candidate_id' => $governorCandidate->id, 'votes' => 8]);

        $this->actingAs($countyAdmin)
            ->get(route('dashboard', [
                'election_type_id' => $presidential->id,
                'status' => 'pending',
                'source' => 'agent',
            ]))
            ->assertOk()
            ->assertSee('Presidential Candidate')
            ->assertSee('Review Station')
            ->assertSee('PENDING');
    }

    public function test_county_admin_cannot_use_super_admin_override(): void
    {
        $countyAdmin = User::factory()->create(['role' => 'county_admin', 'is_active' => true]);
        $agent = User::factory()->create(['role' => 'agent', 'is_active' => true]);
        $county = County::create(['name' => 'Kakamega', 'code' => '037']);
        $constituency = Constituency::create(['county_id' => $county->id, 'name' => 'Lurambi']);
        $ward = Ward::create(['constituency_id' => $constituency->id, 'name' => 'Sheywe']);
        $station = PollingStation::create(['ward_id' => $ward->id, 'name' => 'Protected Station']);
        $electionType = ElectionType::create(['name' => 'Presidential']);
        $submission = VoteSubmission::create([
            'polling_station_id' => $station->id,
            'election_type_id' => $electionType->id,
            'user_id' => $agent->id,
            'agent_name' => $agent->name,
            'agent_code' => 'AGENT-003',
            'status' => 'pending',
            'submitted_at' => now(),
        ]);

        $this->actingAs($countyAdmin)
            ->post(route('votes.override', $submission), [
                'status' => 'verified',
                'notes' => 'Not allowed for county admin',
            ])
            ->assertForbidden();

        $this->actingAs($countyAdmin)
            ->get(route('manage.users.index'))
            ->assertForbidden();

        $this->assertDatabaseHas('vote_submissions', [
            'id' => $submission->id,
            'status' => 'pending',
        ]);
    }
}
