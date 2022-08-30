<?php

namespace App\Services\Banks;

interface CurrencySource
{
    /**
     * @return array{data: array}
     */
    public function getJsonCurrencyTable(): array;
}
