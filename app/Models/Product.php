<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'title', 'sku', 'description'
    ];


    public function variants()
    {
    	return $this->hasMany(ProductVariant::class);
    }
    public function productvariants()
    {
        return $this->belongsToMany(Variant::class,'product_variants','product_id','variant_id'); 
    }
    
    public function prices()
    {
    	return $this->hasMany(ProductVariantPrice::class);
    }
}
