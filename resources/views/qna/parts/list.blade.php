<?php
$cache = [];
function iterate($objs, $handler)
{
    global $cache;
    foreach ($objs as $obj) {
        if (isset($cache[$obj->writer->id])) {
            continue;
        }
        $cache[$obj->writer->id] = true;
        $handler($obj);
    }
}

function clear()
{
    global $cache;
    $cache = [];
}

?>

<ul class="list-group">
    @foreach($qs as $q)
    <a href="/qs/{{ $q->id }}" class="list-group-item">
        <img src="{{ $q->writer->avatar }}" width="16" height="16"/> <b>{{ $q->writer->name }}</b>
        {{ $q->created_at }}
        | 조회수: {{ $q->viewCounts->count() }}
        | 답변수: {{ $q->answers->count() }}
        <?php iterate($q->answers, function ($the) { ?>
            <img src="{{ $the->writer->avatar }}" width="16" height="16"/>
        <?php }) ?>
        <?php iterate($q->comments, function ($the) { ?>
            <img src="{{ $the->writer->avatar }}" width="16" height="16"/>
        <?php }) ?>
        @foreach ($q->answers as $a)
        <?php iterate($a->comments, function ($the) { ?>
            <img src="{{ $the->writer->avatar }}" width="16" height="16"/>
        <?php }) ?>
        @endforeach
        <?php clear() ?>
        <h4>{{ $q->title }}</h4>
    </a>
    @endforeach
</ul>

{!! $qs->links() !!}