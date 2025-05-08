<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceBooking extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function files()
    {
        return $this->hasMany(ServiceImage::class);
    }

    public function serviceReview()
    {
        return $this->hasOne(ServiceBookingReview::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function address()
    {
        return $this->belongsTo(AdditionalAddress::class, 'additional_address_id');
    }

    public function billingAddress()
    {
        return $this->belongsTo(AdditionalAddress::class, 'billing_address_id');
    }

    public function shippingAddress()
    {
        return $this->belongsTo(AdditionalAddress::class, 'shipping_address_id');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'service_booking_id');
    }
}
