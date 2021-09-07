<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsRateToRakuten extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rakutens', function (Blueprint $table) {
            $table->float('rate', 5, 2)->after('price_min')->nullable();
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
            $table->dropColumn('rate');
        });
    }
}
