<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeColumsNullableEbayItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ebay_items', function (Blueprint $table) {
            $table->string('ebay_id', 255)->nullable()->change();
            $table->string('title', 256)->nullable()->change();
            $table->string('price', 255)->nullable()->change();
            $table->longText('image')->nullable()->change();
            $table->longText('error')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ebay_items', function (Blueprint $table) {
            $table->int('ebay_id', 255)->change();
            $table->string('title', 256)->change();
            $table->int('price', 255)->change();
            $table->longText('image')->change();
            $table->longText('error')->change();
        });
    }
}
