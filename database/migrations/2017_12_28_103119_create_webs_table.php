<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWebsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('webs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('url')->unique();
            $table->string('type');
            $table->integer('hasSet')->default(0);
            $table->float('priceChange')->default(1);
            $table->integer('language_id')->default(1);
            $table->integer('brand_id')->default(1);
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
        //
    }
}
