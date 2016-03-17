<?php

namespace ModernPUG\Qna\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Question extends Model
{
    use SoftDeletes;

    protected $table = 'qna_questions';

    protected $fillable = [
        'title', 'content', 'writer_id',
    ];

    public function getMdContentAttribute()
    {
        $content = $this->attributes['content'];
        $parsedown = new \Parsedown();
        return clean($parsedown->text($content));
    }

    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable', 'qna_taggables');
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
            $tagName = strtolower(trim($tagName));
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

    public function getTagsHtmlAttribute()
    {
        $aTags = [];
        foreach ($this->tags as $tag) {
            $name = trim(clean($tag->name));
            if (!$name) {
                continue;
            }
            $item = "<a href='/qs/tags/{$tag->id}'>{$name}</a>";
            array_push($aTags, $item);
        }
        $html = join(', ', $aTags);

        return $html;
    }

    public function getNumVotesWithPadAttribute()
    {
        $grade = $this->votes->sum('grade');
        if (strlen($grade) > 3) {
            $grade = 999;
        }

        return str_pad($grade, 3, "0", STR_PAD_LEFT);
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

    public function viewCounts()
    {
        return $this->hasMany(ViewCount::class, 'q_id');
    }
}
