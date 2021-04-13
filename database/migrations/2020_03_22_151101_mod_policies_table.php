<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModPoliciesTable extends Migration
{
    public function up()
    {
        Schema::table('policies', function (Blueprint $table) {
            $table->integer('type')->default(0);
            $table->string('params')->default(null);
            $table->integer('target_uid')->default(0);
            $table->dropColumn('policy_name');
        });
    }
    
    public function down()
    {
        Schema::table('policies', function (Blueprint $table) {
            $table->dropColumn('type');
            $table->dropColumn('params');
            $table->dropColumn('target_uid');
            $table->string('policy_name')->comment('Also the name of implementaion class.');
        });
    }
}
