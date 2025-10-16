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
        Schema::create('supplier_invoices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('po_id');
            $table->unsignedBigInteger('supplier_id');
            $table->string('invoice_no');
            $table->dateTime('invoice_date');
            $table->decimal('subtotal');
            $table->decimal('tax');
            $table->decimal('discount');
            $table->decimal('total');
            $table->string('currency');
            $table->string('payment_status');
            $table->decimal('paid_amount');
            $table->decimal('due_amount');
            $table->string('status');
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
        Schema::dropIfExists('supplier_invoices');
    }
};
