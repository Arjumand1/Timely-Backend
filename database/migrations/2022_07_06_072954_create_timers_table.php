<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('timers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->string('image')->nullable();
            $table->string('started_at')->nullable();
            $table->string('stopped_at')->nullable();
            $table->bigInteger('total_time')->nullable();
            $table->bigInteger('daily_time')->nullable();
            $table->bigInteger('weekly_time')->nullable();
            $table->bigInteger('monthly_time')->nullable();
            $table->string('captured_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('timers');
    }
};
