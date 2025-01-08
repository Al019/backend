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
        Schema::create('credential_purposes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purpose_id')->constrained('purposes');
            $table->foreignId('reqcred_id')->constrained('request_credentials');
            $table->integer('copy');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credential_purposes');
    }
};
