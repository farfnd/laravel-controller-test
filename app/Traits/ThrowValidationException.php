<?php

namespace App\Traits;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

trait ThrowValidationException
{
    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator, response()->json([
            'status' => 422,
            'error' => $validator->errors(),
        ]));
    }
}
