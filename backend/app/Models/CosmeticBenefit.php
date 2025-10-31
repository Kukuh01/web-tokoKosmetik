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

    /**
     * Function/ORM Not using, because in program not calling CosmeticBenefit table to display data
     */
    // //CosmeticsBenefit<-Many to One->Cosmetics
    public function cosmetics(): BelongsTo
    {
        return $this->belongsTo(Cosmetic::class, 'cosmetic_id');
    }

}
