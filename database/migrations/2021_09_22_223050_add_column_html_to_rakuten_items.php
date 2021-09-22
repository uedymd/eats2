<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnHtmlToRakutenItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rakuten_items', function (Blueprint $table) {
            $table->longText('origin_title')->nullable()->after('jp_title');
            $table->longText('origin_content')->nullable()->after('jp_content');
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
            $table->dropColumn('origin_title');
            $table->dropColumn('origin_content');
        });
    }
}
