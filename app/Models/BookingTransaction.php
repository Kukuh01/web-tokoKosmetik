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

    //Function to generate random unique booking_trx_id
    public static function generateUniqueTrxId(){
        $prefix = 'SHAYNA';
        do {
            $randomString = $prefix . mt_rand(1000, 9999);
        }
        while (
            self::where('booking_trx_id', $randomString)->exists() //looping until same booking_trx_id not exists
        );

        return $randomString;
    }
}
