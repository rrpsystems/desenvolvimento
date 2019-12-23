<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrunksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trunks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('tpbx')->nullable();
            $table->string('trunk')->nullable();
            $table->string('tname')->nullable();
            $table->string('routes_route')->nullable();
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
        Schema::dropIfExists('trunks');
    }
}
