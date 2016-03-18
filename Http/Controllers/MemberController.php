<?php

namespace NineCells\Qna\Http\Controllers;

use DB;
use NineCells\Qna\Models\Answer;
use NineCells\Qna\Models\Comment;
use NineCells\Qna\Models\Question;

class MemberController extends Controller
{
    public function GET_member_qna_info($member_id)
    {
        $grade_qs = DB::select('
    SELECT
      COALESCE(SUM(V.grade),0) grade
    FROM qna_questions Q
    JOIN qna_votes V
    ON V.votable_id = Q.id
    AND V.votable_type = ?
    WHERE writer_id = ?
    AND Q.deleted_at IS NULL
    ', [Question::class, $member_id]);
        $grade_qs = (sizeof($grade_qs) == 0 ? 0 : $grade_qs[0]->grade);

        $grade_as = DB::select('
    SELECT
      COALESCE(SUM(V.grade),0) grade
    FROM qna_answers A
    JOIN qna_votes V
    ON V.votable_id = A.id
    AND V.votable_type = ?
    WHERE writer_id = ?
    AND A.deleted_at IS NULL
    ', [Answer::class, $member_id]);
        $grade_as = (sizeof($grade_as) == 0 ? 0 : $grade_as[0]->grade);

        $grade_cs = DB::select('
    SELECT
      COALESCE(SUM(V.grade),0) grade
    FROM qna_comments C
    JOIN qna_votes V
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

        return view('ncells::qna.pages.member_qna', [
            'member_id' => $member_id,
            'grade_qs' => $grade_qs,
            'grade_as' => $grade_as,
            'grade_cs' => $grade_cs,
            'qs' => $qs,
        ]);
    }
}
