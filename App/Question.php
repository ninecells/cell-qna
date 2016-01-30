<?php

namespace ModernPUG\Qna\App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Question extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title', 'content', 'writer_id',
    ];

    public function getMdContentAttribute()
    {
        $content = $this->attributes['content'];
        $parsedown = new \Parsedown();
        return $parsedown->text($content);
    }

    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    public function getTagsStringAttribute()
    {
        $tags = [];
        foreach ($this->tags as $tag) {
            array_push($tags, $tag->name);
        }
        return join(', ', $tags);
    }

    public function setTagsAttribute($tags)
    {
        $taggable_id = $this->id;
        $taggable_type = self::class;
        // 기존의 태그가 있으면 지우고
        Taggable::where('taggable_id', $taggable_id)
            ->where('taggable_type', $taggable_type)
            ->delete();

        $tags = explode(',', $tags);
        foreach ($tags as $tagName) {
            $tagName = trim($tagName);
            $tag = Tag::firstOrNew(['name' => $tagName]);
            $tag->save();

            $tag_id = $tag->id;
            // 태그를 추가
            Taggable::firstOrNew([
                'tag_id' => $tag_id,
                'taggable_id' => $taggable_id,
                'taggable_type' => $taggable_type,
            ])->save();
        }
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

    public function answers()
    {
        return $this->hasMany(Answer::class, 'q_id');
    }
}
