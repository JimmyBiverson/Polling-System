<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('polling_stations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ward_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('code', 50)->nullable();
            $table->string('presiding_officer')->nullable();
            $table->integer('registered_voters')->default(0);
            $table->timestamps();

            $table->unique(['ward_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('polling_stations');
    }
};
