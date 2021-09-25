<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddViewUrlColumnToEbayItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ebay_items', function (Blueprint $table) {
            $table->string('view_url', 255)->nullable()->after('image');
            $table->timestamp('tracking_at')->nullable();
            $table->dropColumn('supplier_checked_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ebay_items', function (Blueprint $table) {
            $table->dropColumn('view_url');
            $table->dropColumn('tracking_at');
        });
    }
}
