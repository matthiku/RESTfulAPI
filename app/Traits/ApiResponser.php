<?php

namespace App\Traits;

// use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

trait ApiResponser
{

	// create a default JSON response
	private function successResponse($data, $code)
	{
		return response()->json($data, $code);
	}


	protected function errorResponse($message, $code)
	{
        return response()->json([
	            'error' => $message, 
	            'code' => $code,
	        ], $code);
	}



	protected function showAll(Collection $collection, $code = 200)
	{
		if (! $collection->isEmpty()) {

			$transformer = $collection->first()->transformer;

			$collection = $this->filterData($collection, $transformer);
			$collection = $this->sortData($collection, $transformer);
			$collection = $this->paginate($collection, $transformer);
			
			$collection = $this->transformData($collection, $transformer);
		}
		
		return $this->successResponse($collection, $code);
	}



	/**
	 * Return JSON response for one model instance
	 *
     * @param  Model $instance
     * @param  int $mcode
     * @return \Illuminate\Http\Response
	 */
	protected function showOne(Model $instance, $code = 200)
	{
		$transformer = $instance->transformer;
		$instance = $this->transformData($instance, $transformer);

		return $this->successResponse($instance, $code);
	}




	/**
	 * return a simple message 
	 */
	protected function showMessage($message, $code = 200)
	{
		return $this->successResponse(['data' => $message], $code);
	}





	/**
	 * transform data 
	 */
	protected function transformData($data, $transformer)
	{
		$transformation = fractal($data, new $transformer);

		return $transformation->toArray();
	}


	

	/**
	 * sort a colllection 
	 */
	protected function sortData(Collection $collection, $transformer)
	{
		if (request()->has('sort_by')) {
			$attribute = $transformer::OriginalAttribute(request()->sort_by);
			$collection = $collection->sortBy->{$attribute};
		}
		return $collection;
	}



	

	/**
	 * sort a colllection 
	 */
	protected function filterData(Collection $collection, $transformer)
	{
		foreach (request()->query() as $query => $value) {
			$attribute = $transformer::OriginalAttribute($query);
			
			if (isset($attribute, $value)) {
				$collection = $collection->where($attribute, $value);
			}
		}
		return $collection;
	}


	

	/**
	 * sort a colllection 
	 */
	protected function paginate(Collection $collection)
	{
		// make sure the URL params are valid
		$rules = [
			'per_page' => 'integer|min:2|max:50',
		];
		Validator::validate(request()->all(), $rules);

		// establish the current page the request is on (default is 0)
		$page = LengthAwarePaginator::resolveCurrentPage();
		// how many records per page?
		$perPage = request()->has('per_page') ? (int)request()->per_page : 15;

		// get a 'slice' (recordset) with the number of records per page from the collection
		$results = $collection->slice(($page - 1) * $perPage, $perPage)->values();
		// now create the paginated array
		$paginated = new LengthAwarePaginator($results, $collection->count(), $perPage, $page, [
			'path' => LengthAwarePaginator::resolveCurrentPath(),
		]);
		// include other url and query params
		$paginated->appends(request()->all());

		return $paginated;
	}




}