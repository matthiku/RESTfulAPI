<?php

namespace App\Http\Controllers\Category;

use App\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Transformers\CategoryTransformer;

class CategoryController extends ApiController
{

    public function __construct()
    {
        // protection
        $this->middleware('client.credentials')
            ->only(['index', 'show']);
        // protection
        $this->middleware('auth:api')
            ->except(['index', 'show']);
        // make validations work again with transformations
        $this->middleware('transform.input:' . CategoryTransformer::class)
            ->only(['store', 'update']);
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // get all categories
        $categories = Category::all();

        return $this->showAll($categories);
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required',
            'description' => 'required',
        ];
        $this->validate($request, $rules);

        $category = Category::create($request->all());
        return $this->showOne($category, 201);
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        return $this->showOne($category);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Category $category)
    {
        $rules = [
            'name' => 'required_without:description',
            'description' => 'required_without:name',
        ];
        $this->validate($request, $rules);
        
        if ($request->has('name'))
            $category->name = $request->name;
        if ($request->has('description'))
            $category->description = $request->description;

        if ($category->isClean()) {
            return $this->errorResponse('You must specify a value that needs to be changed', 422);            
        }

        $category->save();

        return $this->showOne($category);
    }



    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
        $category->delete();

        return $this->showOne($category);
    }

}
