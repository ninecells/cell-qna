<?php

use App\User;
use ModernPUG\Qna\Models\Question;
use ModernPUG\Qna\Models\Answer;
use ModernPUG\Qna\Models\Comment;

$member = User::find($member_id);

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

?>
@extends('ncells::jumbotron.app')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div>
            @include('ncells::auth.parts.member_tab', ['member_id' => $member->id, 'tabitem_key' => 'qna'])
            <div class="tab-content">
                <br/>
                <div role="tabpanel" class="tab-pane active">
                    <h4>점수:</h4>
                    <ul>
                        <li>질문 점수: {{ $grade_qs }}</li>
                        <li>답변 점수: {{ $grade_as }}</li>
                        <li>댓글 점수: {{ $grade_cs }}</li>
                    </ul>

                    <h4>질문:</h4>
                    @include('mpug::qna.parts.list', ['qs' => $qs])
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
