<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSkuColumnToRakutenItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rakuten_items', function (Blueprint $table) {
            $table->text('ebay_category')->nullable()->after('images');
            $table->text('sku')->nullable()->after('ebay_category');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rakuten_items', function (Blueprint $table) {
            $table->dropColumn('ebay_category');
            $table->dropColumn('sku');
        });
    }
}
