<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class BetPeriod extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("
        ALTER TABLE tournament_bet_events
        ADD period varchar(3) DEFAULT null AFTER handicap, COMMENT '(DC2Type:App\\\\Tournament\\\\Enums\\\\GamePeriod)'
        ");
        DB::statement("UPDATE tournament_bet_events SET period = '100' ");
        DB::statement("ALTER TABLE tournament_bet_events CHANGE period period varchar(3) NOT NULL");
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
