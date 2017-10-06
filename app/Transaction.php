<?php

namespace App;

use App\Buyer;
use App\Product;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{

    // Mass assignement protection
    protected $fillable = [
        'quantity',
        'buyer_id',
        'product_id',
    ];


    public function buyer()
    {
        return $this->belongsToMany(Buyer::class);
    }


    public function products()
    {
        return $this->belongsToMany(Product::class);
    }


}
