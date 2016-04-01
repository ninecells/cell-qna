@extends('ncells::admin.app')

@section('title', '질문 관리')
@section('page-title', '질문 관리')
@section('page-description', '질문을 관리합니다')

@section('content')
<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-body">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th class="rev">#</th>
                        <th class="writer">이름</th>
                        <th class="email">이메일</th>
                        <th class="created">가입</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('head')
@parent
<style>
    table .title {
        width: 100%;
    }

    table .rev, .writer, .email, .created {
        width: auto;
        white-space: nowrap;
    }

    table {
        border-collapse: collapse;
        width: 100%;
    }
</style>
@endsection