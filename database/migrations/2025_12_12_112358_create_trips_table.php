<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('trips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('destination_name')->nullable();
            $table->dateTime('start_time');
            $table->dateTime('expected_end_time');
            $table->dateTime('actual_end_time')->nullable();
            $table->enum('status', ['active', 'completed', 'alerted', 'panic', 'duress_ended'])->default('active');
            $table->enum('trip_type', ['timer', 'panic', 'duress'])->default('timer');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trips');
    }
};
