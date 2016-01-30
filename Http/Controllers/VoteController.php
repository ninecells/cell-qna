<?php

namespace ModernPUG\Qna\Http\Controllers;

use DB;
use ModernPUG\Qna\App\Vote;
use ModernPUG\Qna\App\Answer;
use ModernPUG\Qna\App\Comment;
use ModernPUG\Qna\App\Question;

use Auth;
use Response;
use Illuminate\Http\Request;

class VoteController extends Controller
{
    public function post_up(Request $request)
    {
        return $this->vote($request, 1);
    }

    public function post_down(Request $request)
    {
        return $this->vote($request, -1);
    }

    private function vote($request, $grade)
    {
        $votable_id = $request->input('votable_id');
        $votable_type = $request->input('votable_type');
        switch ($votable_type) {
            case 'question':
                $votable_type = Question::class;
                break;
            case 'answer':
                $votable_type = Answer::class;
                break;
            case 'comment':
                $votable_type = Comment::class;
                break;
        }

        if (Auth::check()) {
            $request->merge(['voter_id' => Auth::user()->id]);
            $request->merge(['votable_type' => $votable_type]);
            $vote = Vote::firstOrNew($request->only([
                'votable_id',
                'votable_type',
                'voter_id'
            ]));
            $vote->grade = $grade;
            $vote->save();
        }

        $voteCount = DB::table('votes')
            ->where([
                'votable_id' => $votable_id,
                'votable_type' => $votable_type,
            ])
            ->sum('grade');

        return Response::json([
            'count' => $voteCount,
        ]);
    }
}
