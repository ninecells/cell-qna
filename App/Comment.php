<?php

namespace ModernPUG\Qna\App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'commentable_id', 'commentable_type', 'content', 'writer_id',
    ];

    public function votes()
    {
        return $this->morphMany(Vote::class, 'votable');
    }

    public function writer()
    {
        return $this->hasOne('App\User', 'id', 'writer_id');
    }

    public function commentable()
    {
        return $this->morphTo();
    }

    public function question()
    {
        return $this->belongsTo(Question::class, 'commentable_id');
    }

    public function answer()
    {
        return $this->belongsTo(Answer::class, 'commentable_id');
    }
}
