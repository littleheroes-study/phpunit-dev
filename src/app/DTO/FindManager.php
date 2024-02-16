<?php

namespace App\DTO;

class FindManager
{
    public $skip;
    public $take;
    const SKIP_COUNT = 0;
    const MAX_PAGE = 30;

    /**
     * 検索クラス
     *
     * @param int $skip
     * @param int $take
     */
    private function __construct(int $skip, int $take)
    {
        $this->skip = $skip;
        $this->take = $take;
    }

    /**
     * 検索クラス値セット
     *
     * @param $pathParams
     * @return static
     */
    public static function setFindParam($pathParams): static
    {
        $skip = isset($pathParams['skip']) && is_numeric($pathParams['skip']) && ctype_digit($pathParams['skip']) ? (int)$pathParams['skip'] : self::SKIP_COUNT;
        $take = isset($pathParams['take']) && is_numeric($pathParams['take']) && ctype_digit($pathParams['take']) ? (int)$pathParams['take'] : self::MAX_PAGE;
        return new self($skip, $take);
    }
}
