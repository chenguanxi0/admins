<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommonCommitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('common_commits', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('language_id')->default(1);
            $table->integer('brand_id')->default(1);
            $table->integer('hasUse')->default(0);
            $table->string('content');
            $table->string('reply');
            $table->timestamp('replyTime');
            $table->string('username');
            $table->boolean('is_admin')->default(1);
            $table->boolean('is_usable')->default(1);
            $table->integer('star')->default(5);
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
