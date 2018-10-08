<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->increments('id');
            $table->string('model')->unique();
            $table->decimal('special_price', 15, 2);
            $table->decimal('price', 15, 2)->nullable();
            $table->decimal('costPrice', 15, 2)->nullable();
            $table->decimal('freight', 15, 2)->nullable();
            $table->float('priceChange')->default(1);
            $table->integer('sureChange')->default(1);
            $table->string('image');
            $table->string('addImages');
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
        Schema::drop('products');
    }
}
