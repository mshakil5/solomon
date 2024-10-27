<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkAssign extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function work()
    {
        return $this->belongsTo(Work::class, 'work_id');
    }

    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }
}
