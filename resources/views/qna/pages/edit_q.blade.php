@extends('app')

@section('content')
<div class="alert alert-info" role="alert">신규 스킨 적용 예정</div>

<ol class="breadcrumb">
    <li><a href="/">홈</a></li>
    <li><a href="/qs">Q&A</a></li>
    <li><a href="/qs/{{ $q->id }}">질문</a></li>
    <li class="active">질문 수정하기</li>
</ol>

<h2>질문 수정하기</h2>

<form method="post" action="/qs/{{ $q->id }}/edit">
    {{ csrf_field() }}
    {{ method_field('PUT') }}
    <div class="form-group">
        <label for="title">제목</label>
        <input type="text" class="form-control" id="title" name="title" placeholder="제목" value="{{ $q->title }}">
    </div>
    <div class="form-group">
        <label for="content">내용</label>
        <textarea class="form-control" id="content" name="content" placeholder="내용"
                  rows="20">{{ $q->content }}</textarea>
    </div>
    <div class="form-group">
        <label for="tags">태그</label>
        <input type="text" class="form-control" id="tags" name="tags" placeholder="태그" value="{{ $q->tagsString }}">
    </div>
    <button type="submit" class="btn btn-default">저장</button>
</form>

@endsection