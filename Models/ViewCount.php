<?php

namespace ModernPUG\Qna\Models;

use Illuminate\Database\Eloquent\Model;

class ViewCount extends Model
{
    protected $fillable = [
        'q_id', 'ip', 'user_id',
    ];
}
