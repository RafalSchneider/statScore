<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStatisticsTable extends Migration
{
    public function up()
    {
        Schema::create('statistics', function (Blueprint $table) {
            $table->id();
            $table->string('match_id');
            $table->string('team_id');
            $table->json('stats')->nullable();
            $table->timestamps();
            $table->unique(['match_id', 'team_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('statistics');
    }
}