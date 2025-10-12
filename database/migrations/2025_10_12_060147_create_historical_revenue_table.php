
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
       Schema::create('historical_revenues', function (Blueprint $table) {
    $table->id();
    $table->year('year');                     
    $table->unsignedTinyInteger('month');     
    $table->unsignedSmallInteger('active_contracts');  
    $table->unsignedSmallInteger('new_contracts')->nullable(); 
        $table->unsignedSmallInteger('expired_contracts')->nullable(); 
    $table->decimal('prev_month_revenue', 15, 2);
    $table->decimal('monthly_revenue', 15, 2);
    $table->date('year_month');
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
