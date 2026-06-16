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
        Schema::create('material_risk_level', function (Blueprint $table) {

            $table->foreignId('material_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('risk_level_id')
                ->constrained()
                ->cascadeOnDelete();

        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('material_risk_level');
    }
};
