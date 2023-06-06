<?php

use App\Models\Batch;
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
        Schema::create('programs', function (Blueprint $table) {
            $table->id();
            $table->string('program_name', 100);
            $table->text('program_desc');
            $table->string('program_categorie', 100);
            $table->string('program_status', 100);
            $table->unsignedBigInteger('batch_id');
            $table->timestamps();

            $table->foreign('batch_id')->references('id')->on('batch')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('programs');
    }
};
