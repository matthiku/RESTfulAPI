<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Validation\ValidationException;

class TransformInput
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $transformer)
    {
        $transformedInput = [];

        // find the original field names for each model
        foreach ($request->request->all() as $input => $value) {
            $transformedInput[$transformer::originalAttribute($input)] = $value;
        }

        // replace the input field names in the request with the "transformed" names
        $request->replace($transformedInput);

        $response = $next($request);

        // in the validation messages, replace the original field names with the transformed names
        if (isset($response->exception) && $response->exception instanceof ValidationException) {
            $data = $response->getData();

            $transformedErrors = [];

            foreach ($data->errors as $field => $errorMsg) {
                $transformedField = $transformer::transformedAttribute($field);
                $transformedErrors[$transformedField] = str_replace($field, $transformedField, $errorMsg);
            }

            $data->errors = $transformedErrors;

            $response->setData($data);
        }

        return $response;
    }
}
