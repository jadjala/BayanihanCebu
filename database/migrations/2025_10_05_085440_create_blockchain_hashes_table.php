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
        Schema::create('blockchain_hashes', function (Blueprint $table) {
            $table->id();
            $table->string('table_name'); // incident_reports, donations, etc.
            $table->unsignedBigInteger('record_id');
            $table->string('hash_value', 64); // SHA-256 hash
            $table->string('previous_hash', 64)->nullable(); // Link to previous hash
            $table->timestamps();
            
            // Composite index for efficient lookups
            $table->index(['table_name', 'record_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blockchain_hashes');
    }
};
