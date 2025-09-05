<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransactionDetails extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'price',
        'cosmetic_id',
        'booking_transaction_id',
        'quantity'
    ];

    //TransactionDetails<-Many to One->BookingTransaction
    public function BookingTransaction(): BelongsTo
    {
        return $this->belongsTo(BookingTransaction::class, 'booking_transaction_id');
    }

    //TransactionDetails<-Many to One->BookingTransaction
    public function Cosmetics(): BelongsTo
    {
        return $this->belongsTo(Cosmetic::class, 'cosmetic_id');
    }
}
