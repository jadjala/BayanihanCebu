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
        Schema::create('incident_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->string('category'); // Fire, Flood, Medical, Crime, etc.
            $table->string('location');
            $table->string('photo_path')->nullable();
            $table->enum('urgency_level', ['Critical', 'High', 'Medium', 'Low'])->default('Medium');
            $table->enum('status', ['Pending', 'Verified', 'Resolved'])->default('Pending');
            $table->text('official_comment')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incidents_report');
    }
};
