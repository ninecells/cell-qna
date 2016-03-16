<?php

namespace ModernPUG\Qna\Models;

use Illuminate\Database\Eloquent\Model;

class Vote extends Model
{
    protected $table = 'qna_votes';

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
