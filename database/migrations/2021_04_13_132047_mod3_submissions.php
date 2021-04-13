<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Mod3Submissions extends Migration
{
    public function up()
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->boolean('success')->default(false);
            $table->boolean('delay')->default(false);
            $table->string('message')->default("");
        });
    }
    
    public function down()
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->dropColumn('success');
            $table->dropColumn('delay');
            $table->dropColumn('message');
        });
    }
}
