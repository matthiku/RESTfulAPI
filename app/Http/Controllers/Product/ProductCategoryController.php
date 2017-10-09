<?php

namespace App\Http\Controllers\Product;

use App\Product;
use App\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class ProductCategoryController extends ApiController
{


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Product $product)
    {
        $categories = $product->categories;

        return $this->showAll($categories);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product, Category $category)
    {
        // interaction with a many-to-many relationships: attach, sync, syncWithoutDetaching methods
        $product->categories()->syncWithoutDetaching([$category->id]);

        return $this->showAll($product->categories);
    }


    /**
     * Delete the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Product $product, Category $category)
    {
        // make sure the product actually has this category attached!
        if ($product->categories()->find($category->id)) {
            // interaction with a many-to-many relationships: attach, sync, syncWithoutDetach methods
            $product->categories()->detach([$category->id]);

            return $this->showAll($product->categories);
        }

        return $this->errorResponse("The product doesn't have this category", 422);
    }


}
