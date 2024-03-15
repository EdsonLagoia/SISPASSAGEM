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
        Schema::create('travelings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user');
            $table->unsignedBigInteger('patient');
            $table->unsignedBigInteger('part');
            $table->unsignedBigInteger('specialty');
            $table->json('companions')->nullable();
            $table->date('date_service');
            $table->string('request', 9)->nullable();
            $table->date('date_going')->nullable();
            $table->date('date_return')->nullable();
            $table->date('date_consult')->nullable();
            $table->string('process');
            $table->boolean('going');
            $table->boolean('return');
            $table->boolean('companion')->nullable();
            $table->longText('obs')->nullable();
            $table->boolean('active');

            $table->foreign('user')->references('id')->on('users');
            $table->foreign('patient')->references('id')->on('patients');
            $table->foreign('part')->references('id')->on('parts');
            $table->foreign('specialty')->references('id')->on('specialties');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('travelings');
    }
};
