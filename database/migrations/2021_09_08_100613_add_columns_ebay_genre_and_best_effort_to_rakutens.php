<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsEbayGenreAndBestEffortToRakutens extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rakutens', function (Blueprint $table) {
            $table->char('ebay_category', 255)->nullable()->after('genre_id');
            $table->char('best_offer', 1)->nullable()->after('price_min');
            $table->char('condition', 1)->nullable()->after('best_offer');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rakutens', function (Blueprint $table) {
            $table->dropColumn('ebay_category');
            $table->dropColumn('best_offer');
            $table->dropColumn('condition');
        });
    }
}
