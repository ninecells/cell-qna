@extends('app')

@section('content')

<style>
    .well {
        background-color: #f4f4f4;
        margin-bottom: 50px;
    }

    .well hr {
        margin: 10px 0px;
    }

    .vote.arrow {
        cursor: pointer;
        cursor: hand;
    }
</style>

<!-- 질문내용 -->
<div class="well well-sm">
    <!-- 수정/삭제 버튼 -->
    <h1 style="margin-top: 0px;">{{ $q->title }}</h1>
    <form method="POST" action="/qs/{{ $q->id }}/delete">
        {{ csrf_field() }}
        {{ method_field('DELETE') }}
        <img src="{{ $q->writer->avatar }}" width="16" height="16"/> <b>{{ $q->writer->name }}</b>
        <a href="/qs/{{ $q->id }}">{{ $q->created_at }}</a>
        | @include('mpug::qna.parts.vote', ['type' => 'question', 'id' => $q->id, 'count' => $q->votes->sum('grade')])
        @can('qna-edit', $q)
        | <a class="btn btn-xs btn-default" href="/qs/{{ $q->id }}/edit">수정</a>
        <button class="btn btn-xs btn-danger">삭제</button>
        @endcan
    </form>
    <hr/>
    {!! $q->md_content !!}
    <p class="text-right">
    <span style="display: inline-block;">
        <img src="{{ $q->writer->avatar }}" width="64" height="64"/>
        <span><b style="vertical-align: top;">{{ $q->writer->name }}</b></span>
    </span>
    </p>
    <p class="text-left">
        <b>태그:</b>
        <?php
        $aTags = [];
        foreach ($q->tags as $tag) {
            $item = "<a href='/qs/tags/{$tag->id}'>{$tag->name}</a>";
            array_push($aTags, $item);
        }
        ?>
        {!! join(', ', $aTags) !!}
    </p>

    <!-- 코멘트 목록 -->
    @foreach($q->comments as $c)
    <hr/>
    <form method="POST" action="/comments/{{ $c->id }}/delete">
        {{ csrf_field() }}
        {{ method_field('DELETE') }}
        <img src="{{ $c->writer->avatar }}" width="16" height="16"/> <b>{{ $c->writer->name }}</b>
        {{ $c->created_at }}
        | @include('mpug::qna.parts.vote', ['type' => 'comment', 'id' => $c->id, 'count' => $c->votes->sum('grade')])
        @can('qna-edit', $c)
        | <a class="btn btn-xs btn-default" href="/comments/{{ $c->id }}/edit">수정</a>
        <button class="btn btn-xs btn-danger">삭제</button>
        @endcan
    </form>
    {{ $c->content }}
    @endforeach

    @can('qna-write')
    <!-- 코멘트 입력 창 -->
    <hr/>
    <form class="form-inline" method="POST" action="/comments/write">
        {{ csrf_field() }}
        <input type="hidden" name="commentable_id" value="{{ $q->id }}"/>
        <input type="hidden" name="commentable_type" value="question"/>
        <div class="form-group">
            <input type="text" class="form-control input-sm" id="content" name="content" placeholder="짧은 답변"/>
        </div>
        <button type="submit" class="btn btn-sm btn-default">작성</button>
    </form>
    @endcan
</div>

<hr/>

<!-- 답변내용 -->
@foreach($q->answers as $a)
<div class="well well-sm">

    <a name="{{ $a->id }}"></a>

    <!-- 수정/삭제 버튼 -->
    <form method="POST" action="/as/{{ $a->id }}/delete">
        {{ csrf_field() }}
        {{ method_field('DELETE') }}
        <img src="{{ $a->writer->avatar }}" width="16" height="16"/> <b>{{ $a->writer->name }}</b>
        <a href="{{ '#'.$a->id }}">{{ $a->created_at }}</a>
        | @include('mpug::qna.parts.vote', ['type' => 'answer', 'id' => $a->id, 'count' => $a->votes->sum('grade')])
        @can('qna-edit', $a)
        | <a class="btn btn-xs btn-default" href="/as/{{ $a->id }}/edit">수정</a>
        <button class="btn btn-xs btn-danger">삭제</button>
        @endcan
    </form>
    <hr/>
    {!! $a->md_content !!}
    <p class="text-right">
    <span style="display: inline-block;">
        <img src="{{ $a->writer->avatar }}" width="64" height="64"/>
        <span><b style="vertical-align: top;">{{ $a->writer->name }}</b></span>
    </span>
    </p>

    <!-- 코멘트 목록 -->
    @foreach($a->comments as $c)
    <hr/>
    <form method="POST" action="/comments/{{ $c->id }}/delete">
        {{ csrf_field() }}
        {{ method_field('DELETE') }}
        <img src="{{ $c->writer->avatar }}" width="16" height="16"/> <b>{{ $c->writer->name }}</b>
        {{ $c->created_at }}
        | @include('mpug::qna.parts.vote', ['type' => 'comment', 'id' => $c->id, 'count' => $c->votes->sum('grade')])
        @can('qna-edit', $c)
        | <a class="btn btn-xs btn-default" href="/comments/{{ $c->id }}/edit">수정</a>
        <button class="btn btn-xs btn-danger">삭제</button>
        @endcan
    </form>
    {{ $c->content }}
    @endforeach

    @can('qna-write')
    <!-- 코멘트 입력 창 -->
    <hr/>
    <form class="form-inline" method="POST" action="/comments/write">
        {{ csrf_field() }}
        <input type="hidden" name="commentable_id" value="{{ $a->id }}"/>
        <input type="hidden" name="commentable_type" value="answer"/>
        <div class="form-group">
            <input type="text" class="form-control input-sm" id="content" name="content" placeholder="코멘트"/>
        </div>
        <button type="submit" class="btn btn-sm btn-default">작성</button>
    </form>
    @endcan
</div>
@endforeach

<!-- 답변하기 창 -->
@can('qna-write')
<form method="POST" action="/as/write">
    {{ csrf_field() }}
    <input type="hidden" name="q_id" value="{{ $q->id }}"/>
    <div class="form-group">
        <label for="content">답변</label>
        <textarea class="form-control" id="content" name="content" placeholder="답변" rows="4"></textarea>
    </div>
    <button type="submit" class="btn btn-default">답변하기</button>
</form>
@endcan

@endsection

@section('script')
<script>
    $(function () {
        $('.vote.up').click(function () {
            var votable_id = $(this).data('id'),
                votable_type = $(this).data('type');
            vote(votable_id, votable_type, 'up');
        });

        $('.vote.down').click(function () {
            var votable_id = $(this).data('id'),
                votable_type = $(this).data('type');
            vote(votable_id, votable_type, 'down');
        });

        function vote(votable_id, votable_type, grade) {
            var formData = {
                votable_id: votable_id,
                votable_type: votable_type
            };

            $.ajax({
                url: "/vote/" + grade,
                type: "POST",
                data: formData,
                success: function (data, textStatus, jqXHR) {
                    $('#label-' + votable_type + '-' + votable_id).text(data.count);
                },
                error: function (jqXHR, textStatus, errorThrown) {

                }
            });
        }
    });
</script>
@endsection
