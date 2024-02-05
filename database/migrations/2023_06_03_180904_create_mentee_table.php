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
        Schema::create('mentee', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary();
            $table->string('name', 100);
            $table->string('gender', 8);
            $table->string('university', 100);
            $table->string('major', 100);
            $table->integer('semester');
            $table->string('email', 100);
            $table->string('phone', 14);
            $table->string('status', 100);
            $table->string('image', 100);
            $table->unsignedBigInteger('group_id')->nullable();
            $table->timestamps();

            $table->foreign('group_id')->references('id')->on('groups')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mentee');
    }
};
