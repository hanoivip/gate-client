<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStatisticTypesTable extends Migration
{
    public function up()
    {
        Schema::create('statistic_types', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('start_time')->default(0);
            $table->integer('end_time')->default(0);
            $table->string('key');
            $table->boolean('disable')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('statistic_types');
    }
}
