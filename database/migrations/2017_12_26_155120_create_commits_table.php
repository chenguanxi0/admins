<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('commits', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('model')->nullable();
            $table->integer('language_id')->default(1);
            $table->string('content');
            $table->string('reply');
            $table->timestamp('replyTime');
            $table->text('img')->nullable();
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
        Schema::dropIfExists('commits');
    }
}
