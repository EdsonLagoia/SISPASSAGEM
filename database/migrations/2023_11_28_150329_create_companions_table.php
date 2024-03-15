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
        Schema::create('companions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('patient');
            $table->string('name', 80);
            $table->string('social_name', 80)->nullable();
            $table->date('birth_date');
            $table->string('sex', 11);
            $table->char('cpf', 14);
            $table->string('rg', 15);
            $table->char('phone', 51)->nullable();
            $table->boolean('active');

            $table->foreign('patient')->references('id')->on('patients');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companions');
    }
};
