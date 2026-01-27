<?php

namespace App\Http\Traits;

trait ApiResponseTrait
{
    
    // Return A Success Json Response

    protected function success($data = null, string $message = null, int $code = 200 )
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], $code);
    }

    // Return An Error Json Response

    protected function error(string $message = null , int $code = 400, $errors = null)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors
        ], $code);
    }

    // Return A Validation Errors Json Response

    protected function validationError($errors, string $message = 'Validation Failed')
    {
        return $this->error($message, 422, $errors);
    }

    // Return A Not Found Json Response

    protected function notFound(string $message = 'Not Found Resource')
    {
        return $this->error($message, 404);
    }

    // Return A Forbidden Json Response

    protected function forbidden(string $message = 'Forbidden')
    {
        return $this->error($message, 403);
    }

    // Return A Created Json Response 
    
    protected function created($data = null, string $message = 'Created Successfully!' )
    {
        return $this->success($data, $message, 201);
    }

    // Return An Updated Json Response 
    protected function updated($data = null, string $message = 'Updated Successfully!')
    {
        return $this->success($data, $message);
    }

    // Return A Delete Json Response

    protected function deleted(string $message = 'Source Deleted Successfully!')
    {
        return $this->success(null, $message);
    }

}
