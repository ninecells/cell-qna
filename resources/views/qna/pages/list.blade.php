@extends('app')

@section('content')
<div class="alert alert-info" role="alert">신규 스킨 적용 예정</div>

<ol class="breadcrumb">
    <li><a href="/">홈</a></li>
    <li class="active">Q&A</li>
</ol>

<div class="well">기능 추가 요청은 <a href="/qs/8">여기</a>에 답변으로 달아주세요.<br/>일정 인원 이상이 참여한, 투표가 많이 된 요청을 우선하여 기능 추가하겠습니다.</div>
<h2>Q&A</h2>

@if(Auth::check())
<p><a class="btn btn-success" href="/qs/write">질문하기</a></p>
@endif

@include('mpug::qna.parts.list', ['qs' => $qs])
@endsection
