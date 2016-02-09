@extends('app')

@section('content')
<ol class="breadcrumb">
    <li><a href="/">홈</a></li>
    <li class="active">Q&A</li>
</ol>

<h2>Q&A</h2>
@if(Auth::check())
<p><a class="btn btn-success" href="/qs/write">질문하기</a></p>
@endif
<table class="table table-bordered">
    <thead>
    <tr>
        <th>Title</th>
        <th>Writer</th>
        <th>조회수</th>
        <th>Created At</th>
    </tr>
    </thead>
    <tbody>
    @foreach($qs as $q)
    <tr>
        <td><a href="/qs/{{ $q->id }}">{{ $q->title }}</a></td>
        <td>
            <img src="{{ $q->writer->avatar }}" width="16px" height="16px" />
            {{ $q->writer->name }}
        </td>
        <td>{{ $q->viewCounts->count() }}</td>
        <td>{{ $q->created_at }}</td>
    </tr>
    @endforeach
    </tbody>
</table>
{!! $qs->links() !!}
@endsection
