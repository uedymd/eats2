<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMikigakkisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mikigakkis', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->longText('url');
            $table->char('ebay_category', 255)->nullable();
            $table->string('ng_keyword', 255)->nullable();
            $table->char('brand_set_id', 255)->nullable();
            $table->char('rate_set_id', 255)->nullable();
            $table->string('ng_url', 255)->nullable();
            $table->char('best_offer', 1)->nullable();
            $table->char('condition', 1)->nullable();
            $table->string('type', 255);
            $table->text('sku')->nullable();
            $table->text('payment_profile')->nullable();
            $table->text('return_profile')->nullable();
            $table->text('shipping_profile')->nullable();
            $table->char('status', 1);
            $table->char('template', 10);
            $table->integer('priority')->nullable()->default(2);
            $table->dateTime('checked_at')->nullable();
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
        Schema::dropIfExists('mikigakkis');
    }
}
