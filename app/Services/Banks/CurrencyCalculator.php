<?php

namespace App\Services\Banks;

interface CurrencyCalculator extends CurrencySource
{
    public function convert(string $from, string $to, float $amount, string $date): array;
}
