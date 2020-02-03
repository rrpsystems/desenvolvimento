<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountcodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accountcodes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('pbxes_id')->nullable();
            $table->string('accountcode')->nullable();
            $table->string('aname')->nullable();
            $table->string('groups_id')->nullable();
            $table->string('tenants_id')->nullable();
            $table->string('sections_id')->nullable();
            $table->string('departaments_id')->nullable();
            $table->string('users_id')->nullable();
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
        Schema::dropIfExists('accountcodes');
    }
}
