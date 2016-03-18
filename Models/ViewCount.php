<?php

namespace NineCells\Qna\Models;

use Illuminate\Database\Eloquent\Model;

class ViewCount extends Model
{
    protected $table = 'qna_view_counts';

    protected $fillable = [
        'q_id', 'ip', 'user_id',
    ];
}
