<?php

namespace App\Traits;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

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
		return $this->successResponse(['data' => $collection], $code);
	}



	/**
	 * Return JSON response for one model instance
	 *
     * @param  Model $model
     * @param  int $mcode
     * @return \Illuminate\Http\Response
	 */
	protected function showOne(Model $model, $code = 200)
	{
		return $this->successResponse(['data' => $model], $code);
	}


}