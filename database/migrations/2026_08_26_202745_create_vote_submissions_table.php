<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vote_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('polling_station_id')->constrained('polling_stations')->cascadeOnDelete();
            $table->foreignId('election_type_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('agent_name');
            $table->string('agent_code', 50);
            $table->string('presiding_officer')->nullable();
            $table->integer('spoilt_votes')->default(0);
            $table->integer('total_votes_cast')->default(0);
            $table->integer('registered_voters')->default(0);
            $table->enum('status', ['pending', 'verified', 'rejected', 'disputed'])->default('pending');
            $table->text('notes')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('device_info')->nullable();
            $table->string('submission_hash', 64)->nullable();
            $table->timestamp('submitted_at');
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['polling_station_id', 'election_type_id']);
            $table->index('status');
            $table->index('submitted_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vote_submissions');
    }
};
