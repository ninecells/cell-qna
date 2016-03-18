<?php

namespace NineCells\Qna\Http\Controllers;

use NineCells\Qna\Models\Answer;
use Illuminate\Http\Request;

use Auth;
use Response;

class AController extends Controller
{
    public function post_write(Request $request)
    {
        $this->authorize('qna-write');
        $request->merge(['writer_id' => Auth::user()->id]);
        $a = Answer::create($request->all());
        return redirect("qs/{$a->q_id}#{$a->id}");
    }

    public function get_edit($a_id)
    {
        $a = Answer::find($a_id);
        $this->authorize('qna-edit', $a);
        return view('mpug::qna.pages.edit_a', ['a' => $a]);
    }

    public function put_edit(Request $request, $a_id)
    {
        $a = Answer::find($a_id);
        $this->authorize('qna-edit', $a);
        $input = $request->only(['content']);
        $a->update($input);
        return redirect("qs/{$a->q_id}#{$a->id}");
    }

    public function delete_item($a_id)
    {
        $a = Answer::find($a_id);
        $this->authorize('qna-edit', $a);
        $a->delete();
        return Response::json(['redirect' => "/qs/{$a->q_id}"]);
    }
}
