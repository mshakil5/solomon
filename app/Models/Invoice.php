<?php

namespace App\Models;

use App\Models\Work;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'amount',
        'img',
        'status',
        'work_id',
        'invoiceid'
    ];

     public function work()
    {
        return $this->belongsTo(Work::class);
    }
    public function serviceBooking()
    {
        return $this->belongsTo(ServiceBooking::class);
    }

    public function transaction()
    {
        return $this->hasOne(Transaction::class, 'invoice_id');
    }

}
