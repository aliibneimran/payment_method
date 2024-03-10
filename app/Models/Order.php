<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    use HasFactory;
    protected $fillable = [
        'total_amount', 'customer_id', 'coupon', 'payment_method'
    ];

    public function order():BelongsTo
    {
      return $this->belongsTo(OrderDetail::class); 
    }

    // public function applicant():HasMany
    // {
    //   return $this->hasMany(Applicant::class); 
    // }
}
