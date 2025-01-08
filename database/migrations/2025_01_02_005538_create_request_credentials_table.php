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
        Schema::create('request_credentials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('credential_id')->constrained('credentials');
            $table->foreignId('request_id')->constrained('requests');
            $table->decimal('price');
            $table->integer('page')->default(1);
            $table->string('reqcred_status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_credentials');
    }
};
