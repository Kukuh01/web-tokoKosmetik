<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CosmeticPhoto extends Model
{
    use SoftDeletes;

    protected $fillable  = [
        'photo',
        'cosmetic_id'
    ];

    //CosmeticsPhotos<-Many To One->cosmetic
    public function cosmetic(): BelongsTo
    {
        return $this->belongsTo(Cosmetic::class, 'cosmetic_id');
    }
}
