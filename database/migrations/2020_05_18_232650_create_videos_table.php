<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVideosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('videos', function (Blueprint $table) {
            $table->id();
            $table->string('video_id');
            $table->foreignId('program_id')->nullable()->constrained();
            $table->string('title')->default('');
            $table->text('description')->nullable();
            $table->string('type');
            $table->string('url')->default('');
            $table->text('thumbnail')->nullable();
            $table->bigInteger('likes')->default(0);
            $table->bigInteger('comments')->default(0);
            $table->bigInteger('views')->default(0);
            $table->bigInteger('duration')->nullable();
            $table->boolean('active')->default(false);
            $table->dateTime("published_on");
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
        Schema::dropIfExists('videos');
    }
}
