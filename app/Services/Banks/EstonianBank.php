<?php

namespace App\Services\Banks;

use Illuminate\Support\Facades\Http;
use function App\Utils\Cache\getCachedOrCacheJsonFromRedis;

final class EstonianBank extends Bank
{
    public static string $alias = "estonian_bank";

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
        $xml_response = Http::get(env('BANK_ESTONIAN_CURRENCY_URL'));

        $xml_string = simplexml_load_string($xml_response);
        $xml_to_json = json_decode(json_encode($xml_string), true);

        return self::prettifyJsonCurrencyTable($xml_to_json)['currencies'];
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
