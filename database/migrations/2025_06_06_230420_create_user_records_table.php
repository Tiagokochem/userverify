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
        Schema::create('user_records', function (Blueprint $table) {
            $table->id();
            $table->string('cpf')->unique();
            $table->string('cep');
            $table->string('email')->unique();
            $table->json('external_data')->nullable(); // dados das APIs externas
            $table->string('risk_level')->nullable(); // ex: low, medium, high
            $table->timestamps();
        });
    }
    
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_records');
    }
};
