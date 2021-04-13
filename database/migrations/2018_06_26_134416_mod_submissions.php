<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModSubmissions extends Migration
{
    public function up()
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->integer('dvalue')->default(0);
        });
    }
    
    public function down()
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->dropColumn('dvalue');
        });
    }
}
