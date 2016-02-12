<?php

namespace ModernPUG\Qna\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'commentable_id', 'commentable_type', 'content', 'writer_id',
    ];

    public function getMdContentAttribute()
    {
        $content = $this->attributes['content'];
        $parsedown = new \Parsedown();
        return $parsedown->text($content);
    }

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

    public function getUrlAttribute()
    {
        $ctype = $this->commentable_type;
        $subUrl = '';
        switch($ctype) {
            case Question::class:
                $subUrl = $this->commentable_id;
                break;
            case Answer::class:
                $subUrl = $this->answer->question->id."#{$this->commentable_id}";
                break;
        }
        return $subUrl;
    }
}
