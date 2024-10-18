<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMaxScoreToAssessmentsTable extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('assessments', 'max_score')) {
            Schema::table('assessments', function (Blueprint $table) {
                $table->integer('max_score')->default(0);
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('assessments', 'max_score')) {
            Schema::table('assessments', function (Blueprint $table) {
                $table->dropColumn('max_score');
            });
        }
    }
}
