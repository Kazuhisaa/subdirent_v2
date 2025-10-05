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
        Schema::create('applications', function (Blueprint $table) {
             $table->id();
             $table->string('first_name', 100);
             $table->string('middle_name', 100);
             $table->string('last_name', 100);
             $table->string('email', 100);
             $table->string('contact_num', 50);
             $table->foreignId('unit_id')->constrained()->onDelete('cascade');
             $table->timestamps();
             $table->softDeletes();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
