<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProfileColumnsToRakutensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rakutens', function (Blueprint $table) {
            $table->text('shipping_profile')->after('sku')->nullable();
            $table->text('return_profile')->after('sku')->nullable();
            $table->text('payment_profile')->after('sku')->nullable();
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
            $table->dropColumn('shipping_profile');
            $table->dropColumn('return_profile');
            $table->dropColumn('payment_profile');
        });
    }
}
