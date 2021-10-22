<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddCategoryToRewards extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::create('rewards', function (Blueprint $table) {
        //     $table->engine = "InnoDB";
        //     $table->charset = "utf8";
        //     $table->collation = "utf8_unicode_ci";

        //     $table->bigIncrements("id");
        //     $table->string('product_code', 18)->unique();
        //     $table->string('product_description');
        //     $table->string('status', 18);
        //     $table->decimal("price", $precision = 8, $scale = 2);
        //     $table->decimal("tax", $precision = 6, $scale = 2);
        //     $table->decimal("insurance", $precision = 6, $scale = 2);
        //     $table->decimal("commission", $precision = 6, $scale = 2);
        //     $table->decimal("delivery_charges", $precision = 6, $scale = 2);
        //     $table->unsignedInteger("credits");
        //     $table->string('image');
        //     $table->timestamp("created_at");
        //     $table->timestamp("updated_at");
        // });

        DB::statement('ALTER TABLE rewards MODIFY product_description VARCHAR(255) NULL ;');
        DB::statement('ALTER TABLE rewards ADD product_name VARCHAR(25) NOT NULL DEFAULT "" AFTER product_code;');
        DB::statement('ALTER TABLE rewards ADD category VARCHAR(25) NOT NULL DEFAULT "" AFTER product_description;');
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
