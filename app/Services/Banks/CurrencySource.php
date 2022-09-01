<?php

namespace App\Services\Banks;

interface CurrencySource
{
    public function getJsonCurrencyTable(string $date): array;
}
