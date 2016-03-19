@extends('ncells::app')

@section('content')
<ol class="breadcrumb">
    <li><a href="/">홈</a></li>
    <li class="active">Q&A</li>
</ol>

<h2>Q&A</h2>

@if(Auth::check())
<p><a class="btn btn-success" href="/qs/write">질문하기</a></p>
@endif

@include('ncells::qna.parts.list', ['qs' => $qs])
@endsection
