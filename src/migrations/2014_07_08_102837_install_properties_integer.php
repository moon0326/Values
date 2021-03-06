<?php

use Illuminate\Database\Migrations\Migration;

class InstallPropertiesInteger extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('properties_integer', function($table) {
            $table->increments('id');
            $table->integer('index_id');
            $table->string('name');
            $table->integer('value');
            $table->engine = 'InnoDB';
            $table->index('index_id');
            $table->index('name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('properties_integer');
    }

}
