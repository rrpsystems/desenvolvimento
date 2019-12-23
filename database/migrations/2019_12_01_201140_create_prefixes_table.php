<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePrefixesTable extends Migration
{
    /**
     * php artisan make:seeder StfcPrefixesTableSeeder
     * php artisan make:seeder SmpPrefixesTableSeeder
     * php artisan make:seeder SmePrefixesTableSeeder
     *        
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prefixes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('prefix');
            $table->string('dadd');
            $table->string('carrier')->nullable();
            $table->string('countrycode')->nullable();
            $table->string('areacode')->nullable();
            $table->string('country')->nullable();
            $table->string('locale')->nullable();
            $table->string('service')->nullable();
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
        Schema::dropIfExists('prefixes');
    }
}
