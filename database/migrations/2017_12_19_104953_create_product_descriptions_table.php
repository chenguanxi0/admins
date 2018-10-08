<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductDescriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_descriptions', function (Blueprint $table) {
            $table->integer('product_model');
            $table->integer('language_id')->default('1');
            $table->integer('brand_id')->default('1');
            $table->integer('status')->default('0');
            $table->integer('commitsNum')->default(5);
            $table->integer('days')->default(5);
            $table->string('product_name');
            $table->string('category_1');
            $table->string('category_2')->nullable();
            $table->string('category_3')->nullable();
            $table->string('category_4')->nullable();
            $table->boolean('is_usable')->default(1);
            $table->string('path');
            $table->string('product_description')->nullable();
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
        Schema::drop('product_descriptions');
    }
}
