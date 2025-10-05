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
       Schema::create('revenue_predictions', function (Blueprint $table) {
    $table->id();
    $table->year('year');                     
    $table->unsignedTinyInteger('month');     
    $table->unsignedSmallInteger('active_contracts');  
    $table->unsignedSmallInteger('new_contracts')->nullable(); 
    $table->decimal('default_rate', 5, 2);     
    $table->decimal('installment_amount', 12, 2); 
    $table->decimal('prev_month_revenue', 15, 2);
    $table->decimal('monthly_revenue', 15, 2);
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('revenue_prediction');
    }
};
