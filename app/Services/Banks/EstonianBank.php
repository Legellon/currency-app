<?php

namespace App\Services\Banks;

use Illuminate\Support\Facades\Http;

class EstonianBank extends Bank
{
    public static string $alias = "estonian_bank";

    public function getJsonCurrencyTable(): array
    {
        $xml_response = Http::get(env('BANK_ESTONIAN_CURRENCY_URL'));

        $xml_string = simplexml_load_string($xml_response);
        $xml_like_json = json_decode(json_encode($xml_string), true);

        $json = $this->prettifyJsonCurrencyTable($xml_like_json)['currencies'];

        return ["data" => $json];
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
