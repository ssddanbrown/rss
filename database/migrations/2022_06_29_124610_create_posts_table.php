<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('feed_id')->index();
            $table->timestamp('published_at')->index();
            $table->string('title', 250);
            $table->text('description');
            $table->string('url', 250);
            $table->string('thumbnail')->default('');
            $table->timestamps();

            $table->unique(['feed_id', 'url']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('posts');
    }
};
