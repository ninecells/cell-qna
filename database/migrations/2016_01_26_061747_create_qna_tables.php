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
        Schema::create('qna_questions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->longText('content');
            $table->integer('writer_id')->index();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('qna_answers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('q_id')->index();
            $table->longText('content');
            $table->integer('writer_id')->index();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('qna_comments', function (Blueprint $table) {
            $table->increments('id');
            $table->morphs('commentable');
            $table->longText('content');
            $table->integer('writer_id')->index();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('qna_tags', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
        });

        Schema::create('qna_taggables', function (Blueprint $table) {
            $table->integer('tag_id')->index();
            $table->morphs('taggable');
        });

        Schema::create('qna_votes', function (Blueprint $table) {
            $table->increments('id');
            $table->morphs('votable');
            $table->integer('grade');
            $table->integer('voter_id')->index();
            $table->timestamps();
            $table->unique(['votable_id', 'votable_type', 'voter_id']);
        });

        Schema::create('qna_view_counts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('q_id')->index();
            $table->string('ip')->index();
            $table->integer('user_id')->index();
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
        Schema::drop('qna_questions');
        Schema::drop('qna_answers');
        Schema::drop('qna_comments');
        Schema::drop('qna_tags');
        Schema::drop('qna_taggables');
        Schema::drop('qna_votes');
        Schema::drop('qna_view_counts');
    }
}
