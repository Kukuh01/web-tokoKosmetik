<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cosmetic extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'thumbnail',
        'about',
        'category_id',
        'is_popular',
        'price',
        'brand_id',
        'stock'
    ];

    //cosmetics<-Many to One->Category
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class,'category_id');
    }

    //cosmetics<-Many to One->brand
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class,'brand_id');
    }

    //cosmetic<-One to Many->cosmeticTestimonials
    public function cosmeticTestimonials(): HasMany
    {
        return $this->hasMany(CosmeticTestimonial::class);
    }

    //cosmetic<-One to Many->cosmeticBenefits
    public function cosmeticBenefits(): HasMany
    {
        return $this->hasMany(CosmeticBenefit::class);
    }

    //cosmetic<-One to Many->cosmeticPhotos
    public function cosmeticPhotos(): HasMany
    {
        return $this->hasMany(CosmeticPhoto::class);
    }

    // Create Slug
    public function setNameAttributes($value){
        $this->attributes['name'] = $value;
        $this->attributes['slug'] = Str::slug($value);
    }
}
