<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQnaTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->longText('content');
            $table->integer('writer_id')->index();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('answers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('q_id')->index();
            $table->longText('content');
            $table->integer('writer_id')->index();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('comments', function (Blueprint $table) {
            $table->increments('id');
            $table->morphs('commentable');
            $table->longText('content');
            $table->integer('writer_id')->index();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('tags', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
        });

        Schema::create('taggables', function (Blueprint $table) {
            $table->integer('tag_id')->index();
            $table->morphs('taggable');
        });

        Schema::create('votes', function (Blueprint $table) {
            $table->increments('id');
            $table->morphs('votable');
            $table->integer('grade');
            $table->integer('voter_id')->index();
            $table->timestamps();
            $table->unique(['votable_id', 'votable_type', 'voter_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('questions');
        Schema::drop('answers');
        Schema::drop('comments');
        Schema::drop('tags');
        Schema::drop('taggables');
        Schema::drop('votes');
    }
}
