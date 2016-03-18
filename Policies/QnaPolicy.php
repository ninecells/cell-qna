<?php

namespace NineCells\Qna\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;

class QnaPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    public function qnaEdit($user, $item)
    {
        return $user->id === $item->writer_id;
    }
}
