@extends('ncells::app')

@section('content')
<style>
    .well {
        background-color: #f4f4f4;
    }

    .well hr {
        margin: 10px 0px;
    }

    .vote.arrow {
        cursor: pointer;
        cursor: hand;
    }
</style>

<ol class="breadcrumb">
    <li><a href="/">홈</a></li>
    <li><a href="/qs">Q&A</a></li>
    <li class="active">질문 보기</li>
</ol>


<!-- 질문내용 -->
<div class="well well-sm">
    <!-- 수정/삭제 버튼 -->
    <h3 style="margin-top: 0px;">{{ $q->title }}</h3>
    @include('ncells::qna.parts.user_small', ['user' => $q->writer])
    | <a href="/qs/{{ $q->id }}">{{ $q->created_at->diffForHumans() }}</a>
    | @include('ncells::qna.parts.vote', ['type' => 'question', 'id' => $q->id, 'count' => $q->votes->sum('grade')])
    | 조회수: {{ $q->viewCounts->count() }}
    | 답변수: {{ $q->answers->count() }}
    @can('qna-edit', $q)
    | <a class="btn btn-xs btn-default" href="/qs/{{ $q->id }}/edit">수정</a>
    <a href="#" data-href="/qs/{{ $q->id }}/delete" class="delete btn btn-xs btn-danger">삭제</a>
    @endcan
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
            $name = clean($tag->name);
            $item = "<a href='/qs/tags/{$tag->id}'>{$name}</a>";
            array_push($aTags, $item);
        }
        ?>
        {!! join(', ', $aTags) !!}
    </p>

    <!-- 코멘트 목록 -->
    @foreach($q->comments as $c)
    <hr/>
    @include('ncells::qna.parts.user_small', ['user' => $c->writer])
    | {{ $c->created_at->diffForHumans() }}
    | @include('ncells::qna.parts.vote_c', ['type' => 'comment', 'id' => $c->id, 'count' => $c->votes->sum('grade')])
    @can('qna-edit', $c)
    | <a class="btn btn-xs btn-default" href="/comments/{{ $c->id }}/edit">수정</a>
    <a href="#" data-href="/comments/{{ $c->id }}/delete" class="delete btn btn-xs btn-danger">삭제</a>
    @endcan
    {!! $c->md_content !!}
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
<br/>
@endcan


<!-- 답변내용 -->
@foreach($q->answers as $a)
<div class="well well-sm">

    <a name="{{ $a->id }}"></a>

    <!-- 수정/삭제 버튼 -->
    @include('ncells::qna.parts.user_small', ['user' => $a->writer])
    | <a href="{{ '#'.$a->id }}">{{ $a->created_at->diffForHumans() }}</a>
    | @include('ncells::qna.parts.vote', ['type' => 'answer', 'id' => $a->id, 'count' => $a->votes->sum('grade')])
    @can('qna-edit', $a)
    | <a class="btn btn-xs btn-default" href="/as/{{ $a->id }}/edit">수정</a>
    <a href="#" data-href="/as/{{ $a->id }}/delete" class="delete btn btn-xs btn-danger">삭제</a>
    @endcan
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
    @include('ncells::qna.parts.user_small', ['user' => $c->writer])
    | {{ $c->created_at->diffForHumans() }}
    | @include('ncells::qna.parts.vote_c', ['type' => 'comment', 'id' => $c->id, 'count' => $c->votes->sum('grade')])
    @can('qna-edit', $c)
    | <a class="btn btn-xs btn-default" href="/comments/{{ $c->id }}/edit">수정</a>
    <a href="#" data-href="/comments/{{ $c->id }}/delete" class="delete btn btn-xs btn-danger">삭제</a>
    @endcan
    {!! $c->md_content !!}
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
@endsection

@section('script')
@parent
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

        $('.btn.delete').click(function () {
            var href = $(this).data('href');

            $.ajax({
                url: href,
                type: "POST",
                data: {_method: 'DELETE'},
                success: function (data, textStatus, jqXHR) {
                    var redirect = data.redirect;
                    if (redirect.indexOf('#') == -1) {
                        window.location.href = redirect;
                    } else {
                        var hash = redirect.split('#')[1];
                        window.location.hash = hash;
                        window.location.reload();
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {}
            });

            return false;
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
