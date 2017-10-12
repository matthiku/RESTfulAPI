<?php

namespace App;

use App\Seller;
use App\Category;
use App\Transaction;
use Illuminate\Database\Eloquent\Model;
use App\Transformers\ProductTransformer;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];

    public $transformer = ProductTransformer::class;

    const UNAVAILABLE_PRODUCT = 'unavailable';
    const AVAILABLE_PRODUCT = 'available';


    // remove unwanted attributes from the result set
    protected $hidden = ['pivot'];


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
        return $this->hasMany(Transaction::class);
    }


    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }


}
