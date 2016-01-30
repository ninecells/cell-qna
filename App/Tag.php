<?php

namespace ModernPUG\Qna\App;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'name',
    ];

    public function questions()
    {
        return $this->morphedByMany(Question::class, 'taggable');
    }
}
