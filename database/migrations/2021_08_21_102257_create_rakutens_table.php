<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRakutensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rakutens', function (Blueprint $table) {
            $table->id();
            $table->string('title', 256);
            $table->string('keyword', 256);
            $table->string('genre', 256)->nullable();
            $table->string('genre_id', 256)->nullable();
            $table->string('ng_keyword', 256)->nullable();
            $table->string('ng_url', 256)->nullable();
            $table->float('price_max', 9, 0)->nullable();
            $table->float('price_min', 9, 0)->nullable();
            $table->string('status', 1);
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
        Schema::dropIfExists('rakutens');
    }
}
