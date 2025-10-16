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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
             $table->foreignId('store_id')->constrained('stores');
    $table->foreignId('customer_id')->constrained('customers');
    $table->foreignId('user_id')->constrained('users');
    $table->dateTime('sale_date');
    $table->decimal('subtotal', 15, 2);
    $table->decimal('tax', 15, 2);
    $table->decimal('discount', 15, 2);
    $table->decimal('total', 15, 2);
    $table->string('status'); // sale_status
    $table->string('payment_status')->default('Unpaid'); // payment_status
    $table->decimal('paid_amount', 15, 2)->default(0.00);
    $table->decimal('credit_amount', 15, 2)->default(0.00);
    $table->string('sale_channel')->nullable(); // sale_channel
    $table->string('delivery_partner')->nullable();
    $table->foreignId('warehouse_id')->nullable()->constrained('warehouses');
    $table->foreignId('promotion_id')->nullable()->constrained('promotions');
    $table->text('note')->nullable();
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
        Schema::dropIfExists('sales');
    }
};
