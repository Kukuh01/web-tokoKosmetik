<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CosmeticTestimonial extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'message',
        'rating',
        'photo',
        'cosmetic_id'
    ];

    //CosmeticTestimonial<-Many to One->Cosmetics
    public function cosmetics(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    //Create slug
    public function setNameAttributes($value){
        $this->attributes['name'] = $value;
        $this->attributes['slug'] = Str::slug($value);
    }
}
