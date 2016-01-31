<?php

namespace ModernPUG\Qna\Models;

use Illuminate\Database\Eloquent\Model;

class Taggable extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'tag_id', 'taggable_id', 'taggable_type',
    ];
}
