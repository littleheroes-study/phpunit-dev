<?php
namespace App\Core;

use App\Enums\StatusCode;
use App\Core\Response;

class Handler extends Response
{
    public static function exceptionFor400()
    {
        (new self())->shutdown(StatusCode::BAD_REQUEST);
        echo json_encode([
            'message' => 'Bad Request',
            'error_code' => StatusCode::BAD_REQUEST
        ]);
        exit();
    }
 
    public static function exceptionFor401()
    {
        (new self())->shutdown(StatusCode::UNAUTHORIZED);
        echo json_encode([
            'message' => 'Unauthorized',
            'error_code' => StatusCode::UNAUTHORIZED
        ]);
        exit();
    }
 
    public static function exceptionFor403()
    {
        (new self())->shutdown(StatusCode::FORBIDDEN);
        echo json_encode([
            'message' => 'Forbidden',
            'error_code' => StatusCode::FORBIDDEN
        ]);
        exit();
    }

    public static function exceptionFor404()
    {
        (new self())->shutdown(StatusCode::NOT_FOUND);
        echo json_encode([
            'message' => 'Not Found',
            'error_code' => StatusCode::NOT_FOUND
        ]);
        exit();
    }

    public static function exceptionFor409()
    {
        (new self())->shutdown(StatusCode::CONFLICT);
        echo json_encode([
            'message' => 'Conflict',
            'error_code' => StatusCode::CONFLICT
        ]);
        exit();
    }

    public static function exceptionFor422(array $validator)
    {
        (new self())->shutdown(StatusCode::UNPROCESSABLE_ENTITY);
        echo json_encode([
            'message' => 'Unprocessable Content',
            'validation_message' => $validator,
            'error_code' => StatusCode::UNPROCESSABLE_ENTITY
        ]);
        exit();
    }

    public static function exceptionFor428()
    {
        (new self())->shutdown(StatusCode::PRECONDITION_REQUIRED);
        echo json_encode([
            'message' => 'Precondition Required',
            'error_code' => StatusCode::PRECONDITION_REQUIRED
        ]);
        exit();
    }

    public function shutdown(int $errorCode)
    {
        $this->responseCode = $errorCode;
        $this->setHeader();
        $this->setResponseCode();
    }
}