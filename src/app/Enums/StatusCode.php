<?php

namespace App\Enums;

final class StatusCode
{
    const OK = 200; // OK
    const CREATED = 201; // Created
    const NO_CONTENT = 204; // No Content
    const BAD_REQUEST = 400; // Bad Request
    const UNAUTHORIZED = 401; // Unauthorized
    const FORBIDDEN = 403; // Forbidden
    const NOT_FOUND = 404; // Not Found
    const CONFLICT = 409; // Conflict
    const UNPROCESSABLE_ENTITY = 422; // Unprocessable Content
    const PRECONDITION_REQUIRED = 428; // Precondition Required
}
