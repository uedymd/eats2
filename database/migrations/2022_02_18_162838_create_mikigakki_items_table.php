<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMikigakkiItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mikigakki_items', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('mikigakki_id');
            $table->string('url', 255);
            $table->string('jp_title', 255);
            $table->longText('origin_title')->nullable();
            $table->longText('jp_content')->nullable();
            $table->longText('origin_content')->nullable();
            $table->string('en_title', 255)->nullable();
            $table->longText('en_content')->nullable();
            $table->text('jp_brand')->nullable();
            $table->text('en_brand')->nullable();
            $table->char('price', 255);
            $table->char('doller', 255)->nullable();
            $table->longtext('images')->nullable();
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
        Schema::dropIfExists('mikigakki_items');
    }
}
