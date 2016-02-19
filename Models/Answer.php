<?php

namespace ModernPUG\Qna\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Answer extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'q_id', 'content', 'writer_id',
    ];

    public function getMdContentAttribute()
    {
        $content = $this->attributes['content'];
        $parsedown = new \Parsedown();
        return clean($parsedown->text($content));
    }

    public function votes()
    {
        return $this->morphMany(Vote::class, 'votable');
    }

    public function writer()
    {
        return $this->hasOne('App\User', 'id', 'writer_id');
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function question()
    {
        return $this->belongsTo(Question::class, 'q_id');
    }
}
