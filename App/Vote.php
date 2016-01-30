<?php

namespace ModernPUG\Qna\App;

use Illuminate\Database\Eloquent\Model;

class Vote extends Model
{
    protected $fillable = [
        'votable_id', 'votable_type', 'grade', 'voter_id',
    ];

    public function voter()
    {
        return $this->hasOne('App\User', 'id', 'voter_id');
    }

    public function votable()
    {
        return $this->morphTo();
    }
}
