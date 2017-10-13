<?php

namespace App\Http\Controllers\Seller;

use App\User;
use App\Seller;
use App\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Transformers\SellerTransformer;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpKernel\Exception\HttpException;

class SellerProductController extends ApiController
{

    public function __construct()
    {
        parent::__construct();

        // make validations work again with transformations
        $this->middleware('transform.input:' . SellerTransformer::class)
            ->only(['store', 'update']);
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Seller $seller)
    {
        $products = $seller->products;

        return $this->showAll($products);
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, User $seller)
    {
        $rules = [
            'name' => 'required',
            'description' => 'required',
            'quantity' => 'required|integer|min:1',
            'image' => 'required|image',
        ];
        $this->validate($request, $rules);

        $data = $request->all();

        $data['status'] = Product::UNAVAILABLE_PRODUCT;
        $data['image'] = $request->image->store('');
        $data['seller_id'] = $seller->id;

        $product = Product::create($data);
        return $this->showOne($product);
    }



    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Seller  $seller
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Seller $seller, Product $product)
    {
        $rules = [
            'quantity' => 'integer|min:1',
            'status' => 'in:' . Product::AVAILABLE_PRODUCT . ',' . Product::UNAVAILABLE_PRODUCT,
            'image' => 'image',
        ];
        $this->validate($request, $rules);

        $this->checkSeller($seller, $product);

        $product->fill(array_filter($request->only([
            'name', 
            'description', 
            'quantity',
        ])));

        // if the change data contains an uploaded image, 
        // delete the existing one and attach the new one to the product
        if ($request->hasFile('image')) {
            Storage::delete($product->image);
            
            $product->image = $request->image->store('');
        }

        if ($request->has('status')) {
            $product->status = $request->status;

            if ($product->isAvailable() && $product->categories->count() == 0) {
                return $this->errorResponse('An active product must have at least one category', 409);
            }
        }

        if ($product->isClean()) {
            return $this->errorResponse('No attribute of this item was changed', 422);
        }

        // save and return the correctly modified product
        $product->save();
        return $this->showOne($product);
    }

    protected function checkSeller(Seller $seller, Product $product)
    {
        if ($seller->id != $product->seller->id) {
            throw new HttpException(422, "The specified seller is not the actual seller of the product");
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Seller  $seller
     * @return \Illuminate\Http\Response
     */
    public function destroy(Seller $seller, Product $product)
    {
        // check if seller actually owns this product
        $this->checkSeller($seller, $product);

        // remove the file linked to this image
        Storage::delete($product->image);

        // now delete the actual product record
        $product->delete();

        return $this->showOne($product);
    }
}
