<?php

namespace App\Services\Banks;

use Illuminate\Support\Facades\Http;

class LithuanianBank extends Bank
{
    public static string $alias = "lithuanian_bank";

    public function getJsonCurrencyTable(): array
    {
        $csv_response = Http::get(env('BANK_LITHUANIAN_CURRENCY_URL'));

        $csv_rows = array_map('str_getcsv', explode("\n", $csv_response));
        $json = $this->getJsonCurrencyTableFromCsv($csv_rows);

        return ["data" => $json];
    }

    private function getJsonCurrencyTableFromCsv(array $csv_rows): array
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
