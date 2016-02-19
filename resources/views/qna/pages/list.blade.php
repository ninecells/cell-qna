@extends('app')

@section('content')
<div class="alert alert-info" role="alert">신규 스킨 적용 예정</div>

<ol class="breadcrumb">
    <li><a href="/">홈</a></li>
    <li class="active">Q&A</li>
</ol>

<h2>Q&A</h2>

@if(Auth::check())
<p><a class="btn btn-success" href="/qs/write">질문하기</a></p>
@endif

@include('mpug::qna.parts.list', ['qs' => $qs])
@endsection
