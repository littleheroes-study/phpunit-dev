<?php
namespace App\Core;

use App\Enums\StatusCode;

class Handler
{
    protected static $directory;
 
    public static function set_config_directory($directory)
    {
        self::$directory = $directory;
    }
 
    public static function exceptionFor400()
    {
        echo json_encode([
            'message' => 'Bad Request',
            'error_code' => StatusCode::BAD_REQUEST
        ]);
    }
 
    public static function exceptionFor401()
    {
        echo json_encode([
            'message' => 'Unauthorized',
            'error_code' => StatusCode::UNAUTHORIZED
        ]);
    }
 
    public static function exceptionFor403()
    {
        echo json_encode([
            'message' => 'Forbidden',
            'error_code' => StatusCode::FORBIDDEN
        ]);
    }

    public static function exceptionFor404()
    {
        echo json_encode([
            'message' => 'Not Found',
            'error_code' => StatusCode::NOT_FOUND
        ]);
    }

    public static function exceptionFor409()
    {
        echo json_encode([
            'message' => 'Conflict',
            'error_code' => StatusCode::CONFLICT
        ]);
    }

    public static function exceptionFor422()
    {
        echo json_encode([
            'message' => 'Unprocessable Content',
            'error_code' => StatusCode::UNPROCESSABLE_ENTITY
        ]);
    }

    public static function exceptionFor428()
    {
        echo json_encode([
            'message' => 'Precondition Required',
            'error_code' => StatusCode::PRECONDITION_REQUIRED
        ]);
    }
}