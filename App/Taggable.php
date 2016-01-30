<?php

namespace ModernPUG\Qna\App;

use Illuminate\Database\Eloquent\Model;

class Taggable extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'tag_id', 'taggable_id', 'taggable_type',
    ];
}
