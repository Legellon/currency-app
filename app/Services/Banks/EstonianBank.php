<?php

namespace App\Services\Banks;

use Illuminate\Support\Facades\Http;

final class EstonianBank extends Bank
{
    public static string $alias = "estonian_bank";

    private static string $currencies_source_link;

    public function __construct()
    {
        parent::__construct(self::$alias);
        self::$currencies_source_link = env('BANK_ESTONIAN_CURRENCIES_URL');
    }

    public function getJsonCurrenciesTable(string $date): array
    {
        $xml_response = Http::get(self::$currencies_source_link . "&imported=$date");

        $xml_string = simplexml_load_string($xml_response);
        $xml_to_json = json_decode(json_encode($xml_string), true);

        $currencies_table = self::prettifyJsonCurrencyTable($xml_to_json);

        return match (array_key_exists('currencies', $currencies_table))
        {
            true  => [$currencies_table['currencies'], false],
            false => [null, true]
        };
    }

    /**
     * Interpolate impure JSON from XML to traditional JSON and a corresponded context
     *
     * You probably will want to unpack returned JSON by 'currencies' key
     *
     * @param array $ugly_json
     * @return array{currencies: array}
     */
    private static function prettifyJsonCurrencyTable(array $ugly_json): array
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
                $object = self::prettifyJsonCurrencyTable($value);

                if (count($object) === 1 &&
                    gettype($single_value = array_slice($object, 0, 1)) == 'array')
                {
                    $json = $single_value;
                    continue;
                }

                match ($key)
                {
                    "@attributes" => $json = array_merge([], $object),
                    default => $json[$key] = $object
                };
            }
            else
            {
                $json[$key] = match ($key)
                {
                    "rate" => floatval($value),
                    default => $value
                };
            }
        }

        return $json;
    }
}
