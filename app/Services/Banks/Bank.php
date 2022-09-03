<?php

namespace App\Services\Banks;

use function App\Utils\Cache\getCachedOrCacheJsonFromRedis;

abstract class Bank implements CurrencyCalculator
{
    use HasAlias;

    protected static string $currencies_table_cache_key;

    public function __construct(string $alias)
    {
        self::$currencies_table_cache_key = $alias . ":currencies_table";
    }

    public function getJsonCurrenciesTableCached(string $date): array
    {
        return getCachedOrCacheJsonFromRedis(
            self::$currencies_table_cache_key . ":$date",
            1000,
            fn() => $this->getJsonCurrenciesTable($date));
    }

    public function convert(string $from, string $to, float $amount, string $date): array
    {
        [[$currencies_table, $err], $from_cache] = $this->getJsonCurrenciesTableCached($date);

        if ($err)
        {
            return [0, true];
        }

        $from_rate = self::findCurrencyRate($from, $currencies_table);
        $to_rate   = self::findCurrencyRate($to, $currencies_table);

        if ($from_rate === false || $to_rate === false)
        {
            return [0, true];
        }

        $exchange_rate = $to_rate / $from_rate;

        return [[$exchange_rate * $amount, false], $from_cache];
    }

    private static function findCurrencyRate(string $currency, array $currencies_table): float|false
    {
        $index = array_search(
            strtoupper($currency),
            array_column($currencies_table['currencies'], 'currency'));

        return $index !== false ? floatval($currencies_table['currencies'][$index]['rate']) : false;
    }
}
