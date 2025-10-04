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
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('location');
            $table->string('unit_code')->unique();
            $table->text('description')->nullable();
            $table->integer('floor_area')->nullable();
            $table->integer('bathroom')->nullable();
            $table->integer('bedroom')->nullable();
            $table->decimal('monthly_rent',10,2)->nullable();
            $table->decimal('unit_price',10,2)->nullable();
            $table->integer('contract_years'); 
            $table->enum('status',['available','rented'])->default('available');
            $table->json('files')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
