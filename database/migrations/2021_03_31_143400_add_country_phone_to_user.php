<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddCountryPhoneToUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE users ADD country_code VARCHAR(3) DEFAULT NULL AFTER referrer_id;');
        #DB::statement('ALTER TABLE users ADD CONSTRAINT FK_COUNTRY_CODE_USERS FOREIGN KEY (country_code) REFERENCES countries (code)');

        DB::statement('ALTER TABLE users ADD phone INT UNSIGNED DEFAULT NULL AFTER country_code;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
