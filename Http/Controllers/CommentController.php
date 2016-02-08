<?php

namespace ModernPUG\Qna\Http\Controllers;

use ModernPUG\Qna\Models\Answer;
use ModernPUG\Qna\Models\Comment;
use ModernPUG\Qna\Models\Question;
use Illuminate\Http\Request;
use Auth;

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
        return redirect('qs/'.$c->url);
    }
}
