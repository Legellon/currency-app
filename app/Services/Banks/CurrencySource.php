<?php

namespace App\Services\Banks;

interface CurrencySource
{
    public function getJsonCurrenciesTableCached(string $date): array;

    public function getJsonCurrenciesTable(string $date): array;
}
