<?php

namespace App\Controllers;

use App\Core\Config;

/**
 * configで管理されている値を取得する
 */
function config(string $value): array
{
    return Config::get($value);
}
