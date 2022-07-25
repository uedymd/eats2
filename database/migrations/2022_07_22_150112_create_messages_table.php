<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->text('Sender');
            $table->text('SendingUserID')->nullable();
            $table->text('RecipientUserID')->nullable();
            $table->text('SendToName')->nullable();
            $table->text('Subject');
            $table->float('MessageID', 16, 0)->unique();
            $table->float('ExternalMessageID', 16, 0)->unique()->nullable();
            $table->dateTimeTz('ReceiveDate', $precision = 0);
            $table->dateTimeTz('ExpirationDate', $precision = 0);
            $table->float('ItemID', 16, 0)->nullable();
            $table->longText('Text')->nullable();
            $table->text('ResponseDetails')->nullable();
            $table->text('MessageType')->nullable();
            $table->dateTimeTz('ItemEndTime', $precision = 0)->nullable();
            $table->text('ItemTitle')->nullable();
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
        Schema::dropIfExists('messages');
    }
}
