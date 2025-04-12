<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MailContent extends Model
{
    use HasFactory;

    public function type()
    {
       return $this->belongsTo(MailContentType::class, 'mail_content_type_id');
    }
}
