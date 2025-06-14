<?php

namespace App\Models;

use App\Models\Work;
use App\Models\Invoice;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function work()
    {
        return $this->belongsTo(Work::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }

    public function booking()
    {
        return $this->belongsTo(ServiceBooking::class, 'booking_id');
    }

}
