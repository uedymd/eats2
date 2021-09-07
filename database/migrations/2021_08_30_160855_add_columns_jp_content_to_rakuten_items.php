<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsJpContentToRakutenItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rakuten_items', function (Blueprint $table) {
            $table->longText('jp_content')->after('jp_title')->nullable();
            $table->string('en_title', 256)->after('jp_content')->nullable();
            $table->longText('en_content')->after('en_title')->nullable();
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
            $table->dropColumn('jp_content');
            $table->dropColumn('en_title');
            $table->dropColumn('en_content');
        });
    }
}
