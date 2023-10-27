<?php

namespace App\Controllers;

use App\Core\Config;

/**
 * configで管理されている値を取得する
 */
function config(string $value): array|string
{
    return Config::get($value);
}

function passwordHash(string $password): string
{
    $hashPasword = password_hash($password, PASSWORD_DEFAULT);
    return $hashPasword;
}
