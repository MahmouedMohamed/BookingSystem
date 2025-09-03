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
        Schema::create('provider_availabilities_overrides', function (Blueprint $table) {
            $table->id();
            $table->foreignId('provider_id')->references('id')->on('users');
            $table->date('date')->nullable();
            $table->tinyInteger('weekday')->nullable();
            // Is this block gonna happen again? (only with weekday)
            // Number of times would it happens again (only with weekday)
            // Ex. Block next 3 mondays from 10:00 to 12:00
            $table->boolean('recurring')->default(false);
            $table->integer('number_of_recurring')->default(0);
            $table->time('start')->nullable();
            $table->time('end')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('provider_availabilities_overrides');
    }
};
