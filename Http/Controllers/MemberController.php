<?php

namespace ModernPUG\Qna\Http\Controllers;

use ModernPUG\Qna\Models\Answer;
use ModernPUG\Qna\Models\Comment;
use ModernPUG\Qna\Models\Question;

class MemberController extends Controller
{
    public function GET_member_qna_info($member_id)
    {
        $qs = Question::where('writer_id', $member_id)
            ->with('viewCounts')
            ->with('comments.writer')
            ->with('writer.socials')
            ->with('answers.writer')
            ->with('answers.comments.writer')
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('mpug::qna.pages.member_qna', [
            'member_id' => $member_id,
            'qs' => $qs
        ]);
    }
}
