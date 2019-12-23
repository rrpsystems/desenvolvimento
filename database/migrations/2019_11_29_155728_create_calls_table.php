<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCallsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('calls', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('pbx')->nullable();
            $table->dateTime('calldate')->nullable();
            $table->string('extensions_id')->nullable();
            $table->string('trunks_id')->nullable();
            $table->string('did')->nullable();
            $table->string('direction')->nullable();
            $table->string('dialnumber')->nullable();
            $table->string('callnumber')->nullable();
            $table->string('prefixes_id')->nullable();
            $table->string('ring')->nullable();
            $table->string('duration')->nullable();
            $table->string('billsec')->nullable();
            $table->string('accountcodes_id')->nullable();
            $table->string('projectcodes_id')->nullable();
            $table->string('disposition')->nullable();
            $table->string('uniqueid')->nullable();
			$table->string('cservice')->nullable();
			$table->string('rates_id')->nullable();
            $table->double('rate', 10, 2)->nullable();
            $table->string('status_id')->nullable();
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
        Schema::dropIfExists('calls');
    }
}
