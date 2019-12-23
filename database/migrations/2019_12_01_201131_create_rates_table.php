<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRatesTable extends Migration
{
   
    public function up()
    {
        Schema::create('rates', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('rname')->nullable();
            $table->string('routes_route')->nullable();
            $table->string('prefixes_service')->nullable();
            $table->string('type')->nullable();
            $table->string('direction')->nullable();
            $table->float('rate', 9, 3)->default('0.000');
            $table->integer('stime')->default('3');
            $table->integer('ttmin')->default('30');
            $table->integer('increment')->default('6');
            $table->float('connection', 9,3)->default('0.000');
            $table->float('vsecond', 9,9)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('rates');
    }
}
