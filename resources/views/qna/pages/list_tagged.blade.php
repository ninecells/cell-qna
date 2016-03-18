@extends('ncells::jumbotron.app')

@section('content')
<ol class="breadcrumb">
    <li><a href="/">홈</a></li>
    <li><a href="/qs">Q&A</a></li>
    <li class="active">태그 검색</li>
</ol>

<h2>태그 검색: '{{ $tag->name }}'</h2>

@if(Auth::check())
<p><a class="btn btn-success" href="/qs/write">질문하기</a></p>
@endif

@include('ncells::qna.parts.list', ['qs' => $qs])
@endsection
