<?php

namespace ModernPUG\Qna\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $table = 'qna_tags';

    public $timestamps = false;

    protected $fillable = [
        'name',
    ];

    public function questions()
    {
        return $this->morphedByMany(Question::class, 'taggable', 'qna_taggables');
    }
}
