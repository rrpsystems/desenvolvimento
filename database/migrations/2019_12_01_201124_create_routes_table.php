<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoutesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('routes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('rpbx')->nullable();
            $table->string('route')->nullable();
            $table->string('ddd')->nullable();
            $table->string('drm')->nullable();  // digitos removidos
            $table->string('dap')->nullable(); // digito adicionado
            $table->string('dialplan')->nullable(); // discagem
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('routes');
    }
}
