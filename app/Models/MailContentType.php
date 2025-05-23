<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MailContentType extends Model
{
    use HasFactory;

    public function mailContent()
    {
        return $this->hasOne(MailContent::class, 'mail_content_type_id');
    }
}
