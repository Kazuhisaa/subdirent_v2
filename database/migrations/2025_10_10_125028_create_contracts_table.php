<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('unit_id')->constrained('units')->onDelete('cascade')->onUpdate('cascade');
            $table->date('contract_start')->nullable();
            $table->date('contract_end')->nullable();
            $table->integer('contract_duration')->nullable();
            $table->decimal('total_price', 12, 2)->nullable();
            $table->decimal('downpayment', 12, 2)->nullable();
            $table->decimal('monthly_payment', 12, 2)->nullable();
            $table->tinyInteger('payment_due_date')->nullable();
            $table->enum('status', ['active', 'completed', 'terminated', 'defaulted'])->default('active');
            $table->text('remarks')->nullable();
            $table->string('contract_pdf')->nullable();
            $table->string('invoice_pdf')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};
