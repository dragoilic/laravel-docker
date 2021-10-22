<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UsersReferrer extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE users ADD referrer_id SMALLINT UNSIGNED DEFAULT NULL;');
        DB::statement('ALTER TABLE users ADD CONSTRAINT FK_REFERRER_ID_USERS FOREIGN KEY (referrer_id) REFERENCES users (id)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}