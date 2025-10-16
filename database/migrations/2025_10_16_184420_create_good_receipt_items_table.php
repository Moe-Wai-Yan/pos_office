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
        Schema::create('good_receipt_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('good_receipt_id');
            $table->unsignedBigInteger('variant_id');
            $table->unsignedBigInteger('lot_id');
            $table->integer('qty_received');
            $table->decimal('unit_cost');
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
        Schema::dropIfExists('good_receipt_items');
    }
};
