<?php

namespace App;

use App\Seller;
use App\Category;
use App\Transaction;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    const UNAVAILABLE_PRODUCT = 'unavailable';
    const AVAILABLE_PRODUCT = 'available';

    // Mass assignement protection
    protected $fillable = [
        'name',
        'description',
        'quantity',
        'status',
        'image',
        'seller_id',
    ];

    public function isAvailable() {
        return $this->status == Product::AVAILABLE_PRODUCT;
    }


    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }


    public function transactions()
    {
        return $this->belongsToMany(Transaction::class);
    }


    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }


}
