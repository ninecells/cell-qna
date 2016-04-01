<?php

namespace NineCells\Qna\Http\Controllers\Admin;

use Auth;
use NineCells\Admin\PackageList;
use NineCells\Qna\Http\Controllers\Controller;
use Response;
use NineCells\Qna\Models\Question;

class AdminController extends Controller
{
    public function __construct(PackageList $packageInfo)
    {
        $this->authorize('admin');

        $packageInfo->setCurrentMenu('qna', [
            [
                'title' => '휴지통',
                'url' => 'admin/qna/trashes'
            ],
        ]);
    }

    public function GET_qna_trashes()
    {
        $qs = Question::onlyTrashed()
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('ncells::qna.pages.admin.qna_trashes', ['qs' => $qs]);
    }
}
