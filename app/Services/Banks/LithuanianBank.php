<?php

namespace App\Services\Banks;

use Illuminate\Support\Facades\Http;
use function App\Utils\Cache\getCachedOrCacheJsonFromRedis;

final class LithuanianBank extends Bank
{
    public static string $alias = "lithuanian_bank";

    private static string $currency_table_key;

    public function __construct()
    {
        self::$currency_table_key = self::$alias . ":currency_table";
    }

    public function getJsonCurrencyTable(): array
    {
        return getCachedOrCacheJsonFromRedis(
            self::$currency_table_key,
            10,
            [self::class, 'downloadCurrencyTable']);
    }

    public static function downloadCurrencyTable(): array
    {
        $csv_response = Http::get(env('BANK_LITHUANIAN_CURRENCY_URL'));
        $csv_rows = array_map('str_getcsv', explode("\n", $csv_response));
        return self::getJsonCurrencyTableFromCsv($csv_rows);
    }

    /**
     * @param array $csv_rows
     * @return array{imported: string, currencies: array}
     */
    private static function getJsonCurrencyTableFromCsv(array $csv_rows): array
    {
        $json = ["imported" => $csv_rows[0][3]];
        $currencies = [];

        foreach ($csv_rows as $value)
        {
            array_push($currencies, [
                "currency" => $value[1],
                "rate" => $value[2]
            ]);
        }

        $json['currencies'] = $currencies;

        return $json;
    }
}
