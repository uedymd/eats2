<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRakutenItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rakuten_items', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('rakuten_id');
            $table->string('url', 256);
            $table->string('jp_title', 256);
            $table->bigInteger('item_code');
            $table->bigInteger('price');
            $table->string('images', 256);
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
        Schema::dropIfExists('rakuten_items');
    }
}
