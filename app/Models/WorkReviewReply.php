<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkReviewReply extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function review()
    {
        return $this->belongsTo(WorkReview::class, 'work_review_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
