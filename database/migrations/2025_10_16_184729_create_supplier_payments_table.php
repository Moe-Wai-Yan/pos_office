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
        Schema::create('supplier_payments', function (Blueprint $table) {
             $table->id();
            $table->foreignId('invoice_id')->constrained('supplier_invoices')->cascadeOnDelete();
            $table->string('method'); // payment_method enum
            $table->decimal('amount', 15, 2);
            $table->string('currency', 10);
            $table->dateTime('paid_at');
            $table->string('ref_no')->nullable();
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
        Schema::dropIfExists('supplier_payments');
    }
};
