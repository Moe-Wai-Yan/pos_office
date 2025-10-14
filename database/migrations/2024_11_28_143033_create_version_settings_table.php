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
        Schema::create('version_settings', function (Blueprint $table) {
            $table->id();
            $table->string('android_version');
            $table->string('ios_version');
            $table->string('playstore_link');
            $table->string('appstore_link');
            $table->string('android_other_link')->nullable();
            $table->string('ios_other_link')->nullable();
            $table->text('release_note')->nullable();
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
        Schema::dropIfExists('version_settings');
    }
};
