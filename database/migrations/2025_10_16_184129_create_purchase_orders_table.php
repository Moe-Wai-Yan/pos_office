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
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('supplier_id');
            $table->unsignedBigInteger('to_location_id');
            $table->string('po_number');
            $table->string('status');
            $table->string('payment_status');
            $table->decimal('paid_amount');
            $table->decimal('due_amount');
            $table->string('currency');
            $table->decimal('exchange_rate');
            $table->dateTime('ordered_at');
            $table->dateTime('expected_at');
            $table->decimal('subtotal');
            $table->decimal('tax');
            $table->decimal('discount');
            $table->decimal('total');
            $table->unsignedBigInteger('user_id');
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
        Schema::dropIfExists('purchase_orders');
    }
};
