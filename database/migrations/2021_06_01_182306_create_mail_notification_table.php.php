<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMailNotificationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mail_notification', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger("user_id");
            $table->string('fullname')->nullable();
            $table->string('title');
            $table->string('subject');
            $table->string("body");
            $table->string("attached_files")->nullable();
            $table->timestamps();

            $table
                ->foreign("user_id")
                ->references("id")
                ->on("users");
        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mail_notification');
    }
}
