<?php

namespace NineCells\Qna\Http\Controllers;

use NineCells\Qna\Models\Question;
use NineCells\Qna\Models\ViewCount;
use NineCells\Qna\Models\Tag;
use Illuminate\Http\Request;

use Auth;
use Gate;
use Response;

class QController extends Controller
{
    public function get_write()
    {
        $this->authorize('qna-write');
        return view('ncells::qna.pages.write');
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
        return view('ncells::qna.pages.edit_q', ['q' => $q]);
    }

    public function put_edit(Request $request, $q_id)
    {
        $q = Question::find($q_id);
        $this->authorize('qna-edit', $q);
        $input = $request->only(['title', 'content']);
        $q->update($input);
        $q->tags = $request->input('tags');
        return redirect("qs/{$q->id}");
    }

    public function delete_item($q_id)
    {
        $q = Question::find($q_id);
        $this->authorize('qna-edit', $q);
        $q->delete();
        return Response::json(['redirect' => '/qs']);
    }

    public function get_list()
    {
        $qs = Question::with('viewCounts')
            ->with('votes')
            ->with('comments.writer')
            ->with('writer.socials')
            ->with('answers.writer')
            ->with('answers.comments.writer')
            ->orderBy('id', 'desc')
            ->paginate(10);
        return view('ncells::qna.pages.list', ['qs' => $qs]);
    }

    public function get_tagged_list($tag_id)
    {
        $tag = Tag::find($tag_id);
        $qs = $tag->questions()
            ->with('viewCounts')
            ->with('comments.writer')
            ->with('writer.socials')
            ->with('answers.writer')
            ->with('answers.comments.writer')
            ->orderBy('id', 'desc')
            ->paginate(10);
        return view('ncells::qna.pages.list_tagged', ['tag' => $tag, 'qs' => $qs]);
    }

    public function get_item(Request $request, $q_id)
    {
        $query = new Question();

        if (Gate::allows('admin')) {
            $query = $query->withTrashed();
        }

        $q = $query->with('writer.socials')
            ->with('tags')
            ->with('viewCounts')
            ->with(['answers' => function ($query) {
                // 답변은 점수 높은 순으로 정렬
                $query->selectRaw('qna_answers.*, COALESCE(SUM(qna_votes.grade),0) AS total_grade')
                    ->leftJoin('qna_votes', function ($join) {
                        $join->on('qna_answers.id', '=', 'qna_votes.votable_id')
                            ->on('qna_votes.votable_type', '=', \DB::raw("'ModernPUG\\\\Qna\\\\Models\\\\Answer'"));
                    })
                    ->groupBy('qna_answers.id')
                    ->orderBy('total_grade', 'desc')
                    ->with('writer.socials')
                    ->with('comments.writer.socials')
                    ->with('comments.votes')
                    ->with('votes');
            }])
            ->with('votes')
            ->with('comments.writer.socials')
            ->with('comments.votes')
            ->find($q_id);

        if (!$q) {
            abort(404);
        }

        // 조회수 증가
        ViewCount::create([
            'q_id' => $q->id,
            'ip' => $request->ip(),
            'user_id' => Auth::check() ? Auth::user()->id : 0,
        ]);

        // 메타 지정
        $desc = strip_tags($q->md_content);
        $desc = str_replace("\r\n", "\n", $desc);
        $desc = str_replace("\r", " ", $desc);
        $desc = str_replace("\n", " ", $desc);
        $desc = $this->limit_words($desc, 30);

        config(['title' => $q->title]);
        config(['author' => $q->writer->name]);
        config(['description' => $desc]);
        config(['keywords' => $q->tagsString]);

        config(['og:title' => $q->title]);
        config(['og:description' => $desc]);

        return view('ncells::qna.pages.item', ['q' => $q]);
    }

    private function limit_words($words, $limit, $append = ' &hellip;')
    {
        // Add 1 to the specified limit becuase arrays start at 0
        $limit = $limit+1;
        // Store each individual word as an array element
        // Up to the limit
        $words = explode(' ', $words, $limit);
        // Shorten the array by 1 because that final element will be the sum of all the words after the limit
        array_pop($words);
        // Implode the array for output, and append an ellipse
        $words = implode(' ', $words) . $append;
        // Return the result
        return $words;
    }
}
