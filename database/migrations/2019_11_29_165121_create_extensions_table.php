<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExtensionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('extensions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('pbxes_id')->nullable();
            $table->string('extension')->nullable();
            $table->string('ename')->nullable();
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
        Schema::dropIfExists('extensions');
    }
}
