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
        Schema::create('variant_attributes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('variant_id');
            $table->unsignedBigInteger('attribute_id');
            $table->string('value');
            $table->unique(['variant_id', 'attribute_id'], 'variant_attribute_unique');

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
        Schema::dropIfExists('variant_attributes');
    }

//      variant_attr_id int [pk, increment]
//   variant_id int [ref: > product_variants.variant_id]
//   attribute_id int [ref: > product_attributes.attribute_id]
//   value varchar
//   Indexes {
//     (variant_id, attribute_id) [unique]
//   }
// }
};
