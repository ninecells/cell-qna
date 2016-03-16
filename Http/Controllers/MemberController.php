<?php

namespace ModernPUG\Qna\Http\Controllers;

use DB;
use ModernPUG\Qna\Models\Answer;
use ModernPUG\Qna\Models\Comment;
use ModernPUG\Qna\Models\Question;

class MemberController extends Controller
{
    public function GET_member_qna_info($member_id)
    {
        $grade_qs = DB::select('
    SELECT
      COALESCE(SUM(V.grade),0) grade
    FROM questions Q
    JOIN votes V
    ON V.votable_id = Q.id
    AND V.votable_type = ?
    WHERE writer_id = ?
    AND Q.deleted_at IS NULL
    ', [Question::class, $member_id]);
        $grade_qs = (sizeof($grade_qs) == 0 ? 0 : $grade_qs[0]->grade);

        $grade_as = DB::select('
    SELECT
      COALESCE(SUM(V.grade),0) grade
    FROM answers A
    JOIN votes V
    ON V.votable_id = A.id
    AND V.votable_type = ?
    WHERE writer_id = ?
    AND A.deleted_at IS NULL
    ', [Answer::class, $member_id]);
        $grade_as = (sizeof($grade_as) == 0 ? 0 : $grade_as[0]->grade);

        $grade_cs = DB::select('
    SELECT
      COALESCE(SUM(V.grade),0) grade
    FROM comments C
    JOIN votes V
    ON V.votable_id = C.id
    AND V.votable_type = ?
    WHERE writer_id = ?
    AND C.deleted_at IS NULL
    ', [Comment::class, $member_id]);
        $grade_cs = (sizeof($grade_cs) == 0 ? 0 : $grade_cs[0]->grade);

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
            'grade_qs' => $grade_qs,
            'grade_as' => $grade_as,
            'grade_cs' => $grade_cs,
            'qs' => $qs,
        ]);
    }
}
