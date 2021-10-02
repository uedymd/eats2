<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropColumsFromDigimarts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('digimarts', function (Blueprint $table) {
            $table->longText('url')->after('title');
            $table->dropColumn('keyword');
            $table->dropColumn('digimart_category');
            $table->dropColumn('price_max');
            $table->dropColumn('price_min');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('digimarts', function (Blueprint $table) {
            $table->dropColumn('url');
            $table->string('keyword');
            $table->integer('digimart_category')->nullable();
            $table->integer('price_max')->nullable();
            $table->integer('price_min')->nullable();
        });
    }
}
