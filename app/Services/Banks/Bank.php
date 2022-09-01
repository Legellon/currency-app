<?php

namespace App\Services\Banks;

abstract class Bank implements CurrencyCalculator
{
    use HasAlias;

    public function convert(string $from, string $to, float $amount, string $date): array
    {
        [[$currency_table, $err], $from_cache] = $this->getJsonCurrencyTable($date);

        if ($err)
        {
            return [0, true];
        }

        $from_rate = self::findCurrencyRate($from, $currency_table);
        $to_rate   = self::findCurrencyRate($to, $currency_table);

        if ($from_rate === false || $to_rate === false)
        {
            return [0, true];
        }

        $exchange_rate = $to_rate / $from_rate;

        return [[$exchange_rate * $amount, false], $from_cache];
    }

    private static function findCurrencyRate(string $currency, array $currency_table): float|false {
        $index = array_search(
            strtoupper($currency),
            array_column($currency_table['currencies'], 'currency'));

        return $index !== false ? floatval($currency_table['currencies'][$index]['rate']) : false;
    }
}
