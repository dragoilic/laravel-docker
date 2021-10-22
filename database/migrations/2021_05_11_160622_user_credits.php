<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UserCredits extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('CREATE TABLE user_credits (
            id INT AUTO_INCREMENT NOT NULL,
            user_id SMALLINT UNSIGNED NOT NULL,
            credits INT NOT NULL,
            paid_date DATETIME NOT NULL,
            reason VARCHAR(50) NOT NULL,
            INDEX IDX_8A757Z985R69U4B5 (user_id),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;');
        DB::statement('ALTER TABLE user_credits ADD CONSTRAINT IDX_8A757Z985R69U4B5 FOREIGN KEY (user_id) REFERENCES users (id);');
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
