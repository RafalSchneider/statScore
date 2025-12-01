<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventsTable extends Migration
{
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->uuid('event_uuid')->nullable(); // idempotency key
            $table->timestamp('timestamp');
            $table->json('data');
            $table->timestamps();
            $table->index(['type', 'timestamp']);
            $table->index(['event_uuid']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('events');
    }
}