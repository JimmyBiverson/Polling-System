<?php

namespace Database\Seeders;

use App\Models\Candidate;
use App\Models\Constituency;
use App\Models\County;
use App\Models\ElectionType;
use App\Models\PollingStation;
use App\Models\User;
use App\Models\VoteDetail;
use App\Models\VoteSubmission;
use App\Models\Ward;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Admin Accounts (admin1 - admin4 + super_admin + county_admin) ──
        $admin = User::create([
            'name' => 'Super Admin',
            'email' => 'admin@polling.go.ke',
            'password' => Hash::make('password'),
            'role' => 'super_admin',
            'phone' => '0700000001',
        ]);

        $admin1 = User::create([
            'name' => 'Admin 1 (Lead Tallying Officer)',
            'email' => 'admin1@polling.go.ke',
            'password' => Hash::make('admin123'),
            'role' => 'super_admin',
            'phone' => '0700000011',
        ]);

        $admin2 = User::create([
            'name' => 'Admin 2 (Electoral Audit Officer)',
            'email' => 'admin2@polling.go.ke',
            'password' => Hash::make('admin123'),
            'role' => 'super_admin',
            'phone' => '0700000012',
        ]);

        $admin3 = User::create([
            'name' => 'Admin 3 (Verification Officer)',
            'email' => 'admin3@polling.go.ke',
            'password' => Hash::make('admin123'),
            'role' => 'super_admin',
            'phone' => '0700000013',
        ]);

        $admin4 = User::create([
            'name' => 'Admin 4 (Data Manager)',
            'email' => 'admin4@polling.go.ke',
            'password' => Hash::make('admin123'),
            'role' => 'super_admin',
            'phone' => '0700000014',
        ]);

        $countyAdmin = User::create([
            'name' => 'County Admin (Kakamega)',
            'email' => 'county@polling.go.ke',
            'password' => Hash::make('password'),
            'role' => 'county_admin',
            'phone' => '0700000002',
        ]);

        $agent1 = User::create([
            'name' => 'Alice Mwangi',
            'email' => 'alice@agent.go.ke',
            'password' => Hash::make('password'),
            'role' => 'agent',
            'phone' => '0712345678',
        ]);

        $agent2 = User::create([
            'name' => 'Bob Ochieng',
            'email' => 'bob@agent.go.ke',
            'password' => Hash::make('password'),
            'role' => 'agent',
            'phone' => '0723456789',
        ]);

        // ── County ─────────────────────────────────────────
        $county = County::create(['name' => 'Kakamega', 'code' => '009']);

        // ── Constituencies ──────────────────────────────────
        $constituenciesData = [
            'Likuyani', 'Lugari', 'Malava', 'Lurambi', 'Navakholo',
            'Mumias West', 'Mumias East', 'Matungu', 'Butere',
            'Khwisero', 'Shinyalu', 'Ikolomani',
        ];

        $constituencies = [];
        foreach ($constituenciesData as $name) {
            $constituencies[$name] = Constituency::create([
                'county_id' => $county->id,
                'name' => $name,
            ]);
        }

        // ── Wards ──────────────────────────────────────────
        $wardsData = [
            'Likuyani' => ['Likuyani', 'Sango', 'Nzoia', 'Sinoko', 'Kongoni'],
            'Lugari' => ['Lumakanda', 'Mautuma', 'Lugari', 'Chekalini', 'Lwandeti', 'Chevaywa'],
            'Malava' => ['West Kabras', 'Chemuche', 'East Kabras', 'Butali/Chegulo', 'Manda/Shivanga', 'Shirugu/Mugai', 'South Kabras'],
            'Lurambi' => ['Butsotso South', 'Butsotso Central', 'Butsotso East', 'Sheywe', 'Shirere', 'Mahiakalo'],
            'Navakholo' => ['Ingotse-matiha', 'Shinoyi-shikomari-esumeiya', 'Bunyala West', 'Bunyala East', 'Bunyala Central'],
            'Mumias West' => ['Mumias Central', 'Mumias North', 'Etenje', 'Musanda'],
            'Mumias East' => ['Lusheya/Lubinu', 'Malaha/Isongo/Makunga', 'East Wanga'],
            'Matungu' => ['Koyonzo', 'Mayoni', 'Kholera', 'Khalaba', 'Namamali'],
            'Butere' => ['Marama West', 'Marama Central', 'Marenyo-shianda', 'Marama North', 'Marama South'],
            'Khwisero' => ['Khwisero', 'Kisa', 'Mukhunyu'],
            'Shinyalu' => ['Shinyalu', 'Isukha', 'Mukumu'],
            'Ikolomani' => ['Ikolomani', 'Idakho', 'Isenye'],
        ];

        $wards = [];
        foreach ($wardsData as $constName => $wardNames) {
            foreach ($wardNames as $wardName) {
                $wards[$wardName] = Ward::create([
                    'constituency_id' => $constituencies[$constName]->id,
                    'name' => $wardName,
                ]);
            }
        }

        // ── Polling Stations ────────────────────────────────
        $stationCount = 0;
        foreach ($wards as $wardName => $ward) {
            $suffixes = ['Primary School', 'Secondary School', 'Academy', 'Center', 'Market'];
            for ($i = 0; $i < 4; $i++) {
                PollingStation::create([
                    'ward_id' => $ward->id,
                    'name' => "{$wardName} {$suffixes[$i]}",
                    'registered_voters' => rand(200, 800),
                ]);
                $stationCount++;
            }
        }

        // Real stations for Sheywe and Shirere
        $realStations = [
            'Sheywe' => [
                'Bondeni Primary School', 'Kakamega Muslim Pri. School',
                'M.o.p.w Chilpark', 'Nabongo Primary School', 'Kakamega Approved School',
            ],
            'Shirere' => [
                'Musaa Primary School', 'Matende Primary School',
                'Apostolic Faith Nursery School', 'Bukhulunya Primary School',
                'Shisasari Primary School', 'Kakamega Township Pri School',
                'Bishop Sulumeti Secondary School',
            ],
        ];

        foreach ($realStations as $wardName => $stations) {
            foreach ($stations as $sName) {
                if (! PollingStation::where('ward_id', $wards[$wardName]->id)->where('name', $sName)->exists()) {
                    PollingStation::create([
                        'ward_id' => $wards[$wardName]->id,
                        'name' => $sName,
                        'registered_voters' => rand(300, 600),
                    ]);
                    $stationCount++;
                }
            }
        }

        // ── Election Types ──────────────────────────────────
        $types = ['Presidential', 'Governor', 'MP', 'Women Rep', 'MCA'];
        $electionTypes = [];
        foreach ($types as $name) {
            $electionTypes[$name] = ElectionType::create(['name' => $name]);
        }

        // ── Candidates ──────────────────────────────────────
        $presidentialCandidates = [
            ['name' => 'William Ruto', 'party' => 'UDA'],
            ['name' => 'Raila Odinga', 'party' => 'ODM'],
            ['name' => 'George Wajackoyah', 'party' => 'ROOTS'],
            ['name' => 'David Waihiga', 'party' => 'AP'],
        ];

        foreach ($presidentialCandidates as $c) {
            Candidate::create([
                'election_type_id' => $electionTypes['Presidential']->id,
                'name' => $c['name'],
                'party' => $c['party'],
            ]);
        }

        $governorCandidates = [
            ['name' => 'Fernandes Barasa', 'party' => 'ODM'],
            ['name' => 'Edward Sifuna', 'party' => 'ODM'],
            ['name' => 'Cleophas Malala', 'party' => 'ANC'],
        ];

        foreach ($governorCandidates as $c) {
            Candidate::create([
                'election_type_id' => $electionTypes['Governor']->id,
                'name' => $c['name'],
                'party' => $c['party'],
            ]);
        }

        // Generic candidates for MP, Women Rep, MCA
        foreach (['MP', 'Women Rep', 'MCA'] as $type) {
            for ($i = 1; $i <= 5; $i++) {
                Candidate::create([
                    'election_type_id' => $electionTypes[$type]->id,
                    'name' => "Candidate {$type}-{$i}",
                    'party' => ['UDA', 'ODM', 'ANC', 'FORD-K', 'Independent'][$i - 1],
                ]);
            }
        }

        // ── Initial Seed Submissions for Governor Election ──
        $govCandidates = Candidate::where('election_type_id', $electionTypes['Governor']->id)->get();
        $sampleStations = PollingStation::with('ward.constituency')->take(40)->get();

        foreach ($sampleStations as $idx => $station) {
            $b1 = rand(150, 420);
            $b2 = rand(100, 310);
            $b3 = rand(50, 180);
            $spoilt = rand(2, 18);
            $totalCast = $b1 + $b2 + $b3 + $spoilt;
            $registered = max($station->registered_voters, $totalCast + rand(50, 200));

            $submission = VoteSubmission::create([
                'polling_station_id' => $station->id,
                'election_type_id' => $electionTypes['Governor']->id,
                'user_id' => $admin1->id,
                'agent_name' => 'Agent '.($idx + 1),
                'agent_code' => 'E0987'.str_pad($idx, 4, '0', STR_PAD_LEFT),
                'presiding_officer' => 'PO Officer '.chr(65 + ($idx % 26)),
                'spoilt_votes' => $spoilt,
                'total_votes_cast' => $totalCast,
                'registered_voters' => $registered,
                'status' => $idx % 5 === 0 ? 'pending' : 'verified',
                'ip_address' => '127.0.0.1',
                'submitted_at' => now()->subMinutes(rand(5, 480)),
            ]);

            if ($govCandidates->count() >= 3) {
                VoteDetail::create(['vote_submission_id' => $submission->id, 'candidate_id' => $govCandidates[0]->id, 'votes' => $b1]);
                VoteDetail::create(['vote_submission_id' => $submission->id, 'candidate_id' => $govCandidates[1]->id, 'votes' => $b2]);
                VoteDetail::create(['vote_submission_id' => $submission->id, 'candidate_id' => $govCandidates[2]->id, 'votes' => $b3]);
            }

            $submission->submission_hash = $submission->generateHash();
            $submission->save();
        }

        echo 'Seeded: 8 users, 1 county, 12 constituencies, '.count($wards)." wards, {$stationCount} stations, 5 election types, ".Candidate::count().' candidates, and '.VoteSubmission::count()." vote submissions.\n";
    }
}
