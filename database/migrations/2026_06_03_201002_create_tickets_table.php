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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            //Als user verwijdert wordt, ticket blijft bestaan en id = null
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            //zal nog komen orders moeten eerst nog aangemaakt worden
//            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->unsignedBigInteger('order_id')->nullable();

            $table->string('subject');
            $table->text('description');
            $table->string('status')->default('Open');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
