<?php

namespace App\Services\Banks;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redis;

final class EstonianBank extends Bank
{
    public static string $alias = "estonian_bank";

    private string $currency_table_key;

    public function __construct()
    {
        $this->currency_table_key = self::$alias . ":currency_table";
    }

    public function getJsonCurrencyTable(): array
    {
        $currency_table = Redis::get($this->currency_table_key);

        if (is_null($currency_table))
        {
            $xml_response = Http::get(env('BANK_ESTONIAN_CURRENCY_URL'));

            $xml_string = simplexml_load_string($xml_response);
            $xml_to_json = json_decode(json_encode($xml_string), true);

            $currency_table = $this->prettifyJsonCurrencyTable($xml_to_json)['currencies'];

            Redis::set($this->currency_table_key, json_encode($currency_table), 'EX', 3600);
        }
        else
        {
            $currency_table = json_decode($currency_table, true);
        }

        return $currency_table;
    }

    /**
     * @param array $ugly_json
     * @return array{currencies: array}
     */
    private function prettifyJsonCurrencyTable(array $ugly_json): array
    {
        $json = [];

        foreach ($ugly_json as $key => $value)
        {
            $key = match ($key)
            {
                "Cube" => "currencies",
                default => $key
            };

            if (gettype($value) === 'array')
            {
                $object = $this->prettifyJsonCurrencyTable($value);

                if (count($object) === 1)
                {
                    $single_value = array_slice($object, 0, 1);
                    if (gettype($single_value) === 'array')
                    {
                        $json = $single_value;
                        continue;
                    }
                }

                match ($key)
                {
                    "@attributes" => $json = array_merge([], $object),
                    default => $json[$key] = $object
                };
            }
            else
            {
                $json[$key] = $value;
            }
        }

        return $json;
    }
}
