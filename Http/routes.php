<?php

Route::group(['middleware' => ['web']], function () {

    Route::group(['namespace' => 'NineCells\Qna\Http\Controllers'], function() {

        Route::get('qs/write', 'QController@get_write');
        Route::post('qs/write', 'QController@post_write');
        Route::get('qs/{q_id}/edit', 'QController@get_edit');
        Route::put('qs/{q_id}/edit', 'QController@put_edit');
        Route::delete('qs/{q_id}/delete', 'QController@delete_item');
        Route::get('qs/tags/{tag_id}', 'QController@get_tagged_list');
        Route::get('qs', 'QController@get_list');
        Route::get('qs/{q_id}', 'QController@get_item');

        Route::post('as/write', 'AController@post_write');
        Route::get('as/{a_id}/edit', 'AController@get_edit');
        Route::put('as/{a_id}/edit', 'AController@put_edit');
        Route::delete('as/{a_id}/delete', 'AController@delete_item');

        Route::post('comments/write', 'CommentController@post_write');
        Route::get('comments/{c_id}/edit', 'CommentController@get_edit');
        Route::put('comments/{c_id}/edit', 'CommentController@put_edit');
        Route::delete('comments/{c_id}/delete', 'CommentController@delete_item');

        Route::post('vote/up', 'VoteController@post_up');
        Route::post('vote/down', 'VoteController@post_down');
    });

    Route::group(['namespace' => 'NineCells\Qna\Http\Controllers'], function() {

        Route::get('members/{member_id}/qna', 'MemberController@GET_member_qna_info')->name('ncells::url.qna.member_qna');
    });

    Route::group(['prefix' => 'admin/qna', 'namespace' => 'NineCells\Qna\Http\Controllers\Admin'], function() {

        Route::get('trashes', 'AdminController@GET_qna_trashes');
    });
});
