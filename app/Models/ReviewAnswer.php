<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReviewAnswer extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function review()
    {
        return $this->belongsTo(WorkReview::class, 'work_review_id');
    }

    public function question()
    {
        return $this->belongsTo(ReviewQuestion::class, 'review_question_id');
    }
}
