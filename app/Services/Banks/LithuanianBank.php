<?php

namespace App\Services\Banks;

use Illuminate\Support\Facades\Http;

final class LithuanianBank extends Bank
{
    public static string $alias = "lithuanian_bank";

    private static string $currencies_source_link;

    public function __construct()
    {
        parent::__construct(self::$alias);
        self::$currencies_source_link = env('BANK_LITHUANIAN_CURRENCIES_URL');
    }

    public function getJsonCurrenciesTable(string $date): array
    {
        $csv_response = Http::get(self::$currencies_source_link . "&dte=$date");
        $csv_rows = array_map('str_getcsv', explode("\n", $csv_response));
        return [self::getJsonCurrenciesTableFromCsv($csv_rows), false];
    }

    /**
     * @param array $csv_rows
     * @return array{imported: string, currencies: array}
     */
    private static function getJsonCurrenciesTableFromCsv(array $csv_rows): array
    {
        $json = ["imported" => $csv_rows[0][3]];
        $currencies = [];

        foreach ($csv_rows as $value)
        {
            array_push($currencies, [
                "currency" => $value[1],
                "rate" => floatval($value[2])
            ]);
        }

        $json['currencies'] = $currencies;

        return $json;
    }
}
