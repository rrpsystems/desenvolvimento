<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePbxesTable extends Migration
{
    
    public function up()
    {
        Schema::create('pbxes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('model');
            $table->string('connection');
            $table->string('host')->nullable();
            $table->string('port')->nullable();
            $table->string('user')->nullable();
            $table->string('password')->nullable();
            $table->string('interval')->nullable();
            $table->timestamps();
        });
    }

    
    public function down()
    {
        Schema::dropIfExists('pbxes');
    }
}
