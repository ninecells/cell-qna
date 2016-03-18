<?php

namespace NineCells\Qna\Http\Controllers;

use NineCells\Qna\Models\Answer;
use NineCells\Qna\Models\Comment;
use NineCells\Qna\Models\Question;
use Illuminate\Http\Request;

use Auth;
use Response;

class CommentController extends Controller
{
    public function post_write(Request $request)
    {
        $this->authorize('qna-write');
        $request->merge(['writer_id' => Auth::user()->id]);
        $ctype = $request->input('commentable_type');
        switch($ctype) {
            case 'question':
                $ctype = Question::class;
                break;
            case 'answer':
                $ctype = Answer::class;
                break;
        }
        $request->merge(['commentable_type' => $ctype]);
        $c = Comment::create($request->all());
        return redirect('qs/'.$c->url);
    }

    public function get_edit($c_id)
    {
        $c = Comment::find($c_id);
        $this->authorize('qna-edit', $c);
        return view('mpug::qna.pages.edit_c', ['c' => $c]);
    }

    public function put_edit(Request $request, $c_id)
    {
        $c = Comment::find($c_id);
        $this->authorize('qna-edit', $c);
        $input = $request->only(['content']);
        $c->update($input);
        return redirect('qs/'.$c->url);
    }

    public function delete_item($c_id)
    {
        $c = Comment::find($c_id);
        $this->authorize('qna-edit', $c);
        $c->delete();
        return Response::json(['redirect' => '/qs/'.$c->url]);
    }
}
