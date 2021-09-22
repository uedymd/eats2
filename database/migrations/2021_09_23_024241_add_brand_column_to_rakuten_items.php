<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBrandColumnToRakutenItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rakuten_items', function (Blueprint $table) {
            $table->text('jp_brand')->nullable()->after('en_content');
            $table->text('en_brand')->nullable()->after('jp_brand');
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
            $table->dropColumn('jp_brand');
            $table->dropColumn('en_brand');
        });
    }
}
