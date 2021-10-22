<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRewardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rewards', function (Blueprint $table) {
            $table->engine = "InnoDB";
            $table->charset = "utf8";
            $table->collation = "utf8_unicode_ci";

            $table->bigIncrements("id");
            $table->string('product_code', 18)->unique();
            $table->string('product_description');
            $table->string('status', 18);
            $table->decimal("price", $precision = 8, $scale = 2);
            $table->decimal("tax", $precision = 6, $scale = 2);
            $table->decimal("insurance", $precision = 6, $scale = 2);
            $table->decimal("commission", $precision = 6, $scale = 2);
            $table->decimal("delivery_charges", $precision = 6, $scale = 2);
            $table->unsignedInteger("credits");
            $table->string('image');
            $table->timestamp("created_at");
            $table->timestamp("updated_at");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rewards');
    }
}
