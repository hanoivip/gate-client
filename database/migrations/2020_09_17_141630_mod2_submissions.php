<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Mod2Submissions extends Migration
{
    public function up()
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->integer('penalty')->default(0);
            $table->integer('final_value')->default(0);
            $table->integer('status')->default(0)->comment('Transaction status..');
        });
    }
    
    public function down()
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->dropColumn('penalty');
            $table->dropColumn('final_value');
            $table->dropColumn('status');
        });
    }
}
