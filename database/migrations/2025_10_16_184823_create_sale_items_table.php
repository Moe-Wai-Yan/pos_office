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
        Schema::create('sale_items', function (Blueprint $table) {
            $table->id();
             $table->foreignId('sale_id')->constrained('sales')->cascadeOnDelete();
    $table->foreignId('variant_id')->constrained('product_variants');
    $table->foreignId('lot_id')->constrained('lots');
    $table->integer('quantity');
    $table->decimal('unit_price', 15, 2);
    $table->string('price_name')->nullable();
    $table->string('discount_type')->nullable();
    $table->decimal('discount_value', 15, 2)->default(0);
    $table->foreignId('tax_rate_id')->nullable()->constrained('tax_rates');
    $table->decimal('line_total', 15, 2);
    $table->string('currency', 10);
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
        Schema::dropIfExists('sale_items');
    }
};
