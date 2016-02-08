<?php

namespace ModernPUG\Qna\Http\Controllers;

use ModernPUG\Qna\Models\Question;
use ModernPUG\Qna\Models\Tag;
use Illuminate\Http\Request;

use App\Http\Requests;
use Auth;

class QController extends Controller
{
    public function get_write()
    {
        $this->authorize('qna-write');
        return view('mpug::qna.pages.write');
    }

    public function post_write(Request $request)
    {
        $this->authorize('qna-write');
        $request->merge(['writer_id' => Auth::user()->id]);

        $q = Question::create($request->all());
        // 위의 create에서 tags를 바로 넣지 않고 여기서 추가로 save를 해준다
        // 위에서 만들어진 Question 의 id 가 tags 입력 시 필요하기 때문
        $q->tags = $request->input('tags');
        $q->save();

        return redirect("qs/{$q->id}");
    }

    public function get_edit($q_id)
    {
        $q = Question::find($q_id);
        $this->authorize('qna-edit', $q);
        return view('mpug::qna.pages.edit_q', ['q' => $q]);
    }

    public function put_edit(Request $request, $q_id)
    {
        $q = Question::find($q_id);
        $this->authorize('qna-edit', $q);
        $input = $request->only(['title', 'content']);
        $q->where('id', $q_id)->update($input);
        $q->tags = $request->input('tags');
        return redirect("qs/{$q->id}");
    }

    public function delete_item($q_id)
    {
        $q = Question::find($q_id);
        $this->authorize('qna-edit', $q);
        $q->delete();
        return redirect('qs');
    }

    public function get_list()
    {
        $qs = Question::with('writer')->orderBy('id', 'desc')->paginate(10);
        return view('mpug::qna.pages.list', ['qs' => $qs]);
    }

    public function get_tagged_list($tag_id)
    {
        $tag = Tag::find($tag_id);
        $qs = $tag->questions()
            ->with('writer')
            ->orderBy('id', 'desc')
            ->paginate(10);
        return view('mpug::qna.pages.list_tagged', ['tag' => $tag, 'qs' => $qs]);
    }

    public function get_item($q_id)
    {
        $q = Question::with(['answers' => function ($query) {
            // 답변은 점수 높은 순으로 정렬
            $query->selectRaw('answers.*, COALESCE(SUM(votes.grade),0) AS total_grade')
                ->leftJoin('votes', function ($join) {
                    $join->on('answers.id', '=', 'votes.votable_id')
                        ->on('votes.votable_type', '=', \DB::raw("'ModernPUG\\\\Qna\\\\Models\\\\Answer'"));
                })
                ->groupBy('answers.id')
                ->orderBy('total_grade', 'desc')
                ->with('votes');
        }])
            ->with('comments.votes')
            ->with('votes')
            ->find($q_id);

        if (!$q) {
            abort(404);
        }

        config(['title' => $q->title . ' - modernpug.org']);
        return view('mpug::qna.pages.item', ['q' => $q]);
    }
}
