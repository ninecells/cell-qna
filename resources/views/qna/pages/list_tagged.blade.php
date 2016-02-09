@extends('app')

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

<ul class="list-group">
    @foreach($qs as $q)
    <a href="/qs/{{ $q->id }}" class="list-group-item">
        <img src="{{ $q->writer->avatar }}" width="16" height="16"/> <b>{{ $q->writer->name }}</b>
        {{ $q->created_at }}
        | 조회수: {{ $q->viewCounts->count() }}
        <h4>{{ $q->title }}</h4>
    </a>
    @endforeach
</ul>

{!! $qs->links() !!}
@endsection
