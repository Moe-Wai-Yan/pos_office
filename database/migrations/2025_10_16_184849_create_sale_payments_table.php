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
        Schema::create('sale_payments', function (Blueprint $table) {
            $table->id();
             $table->foreignId('sale_id')->constrained('sales')->cascadeOnDelete();
    $table->string('method');
    $table->decimal('amount', 15, 2);
    $table->string('currency', 10);
    $table->dateTime('paid_at');
    $table->string('ref_no')->nullable();
    $table->foreignId('giftcard_id')->nullable()->constrained('gift_cards');
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
        Schema::dropIfExists('sale_payments');
    }
};
