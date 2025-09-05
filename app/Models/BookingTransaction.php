<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class BookingTransaction extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'booking_trx_id',
        'proof',
        'total_amount',
        'total_tax_amount',
        'is_paid',
        'address',
        'city',
        'sub_total_amount',
        'quantity'
    ];

    //BookingTransaction<-One to Many->TransactionDetails
    public function transactionDetails(): HasMany
    {
        return $this->hasMany(TransactionDetails::class);
    }
}
