<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEbayItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ebay_items', function (Blueprint $table) {
            $table->id();
            $table->char('ebay_id', 255);
            $table->string('site', 256);
            $table->char('supplier_id', 255);
            $table->string('title', 256);
            $table->char('price', 255);
            $table->longText('image');
            $table->longText('error');
            $table->timestamp('supplier_checked_id')->nullable();
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
        Schema::dropIfExists('ebay_items');
    }
}
