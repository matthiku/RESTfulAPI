<?php

namespace App;

use App\Product;
use Illuminate\Database\Eloquent\Model;
use App\Transformers\CategoryTransformer;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
	use SoftDeletes;
	protected $dates = ['deleted_at'];

    public $transformer = CategoryTransformer::class;

    // remove unwanted attributes from the result set
    protected $hidden = ['pivot'];


    // Mass assignement protection
    protected $fillable = [
        'name',
        'description',
    ];



    public function products()
    {
        return $this->belongsToMany(Product::class);
    }

}
