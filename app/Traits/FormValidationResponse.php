<?php

namespace App\Traits;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

/*
|--------------------------------------------------------------------------
| Api Form Validation Responser Trait
|--------------------------------------------------------------------------
|
| This trait will be used for validation response we sent to clients.
|
*/

trait FormValidationResponse
{
    /**
     * Return a validation error JSON response.
     *
     * @param array|string $validators
     * @param string|null $response
     * @return JsonResponse
     */
    public function failedValidation(\Illuminate\Contracts\Validation\Validator $validator) // Built in form validation override
    {
        $response = new Response(
            ['status'=>'Error',
            'statusCode' => Response::HTTP_BAD_REQUEST,
            'message' => $validator->errors()
        ], Response::HTTP_BAD_REQUEST);

        throw new ValidationException($validator, $response);
    }
}
