<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CosmeticBenefit extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'cosmetic_id'
    ];

    //CosmeticsBenefit<-Many to One->Cosmetics
    public function cosmetics(): BelongsTo
    {
        return $this->belongsTo(Cosmetic::class, 'cosmetic_id');
    }

}
