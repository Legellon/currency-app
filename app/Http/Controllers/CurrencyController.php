<?php

namespace App\Http\Controllers;

use App\Services\Banks\BankFactory;
use App\Services\Banks\EstonianBank;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class CurrencyController extends Controller
{
    //TODO: add validation
    //TODO: improve error handling
    public function getTable(Request $request): JsonResponse
    {
        $bank = $request->query('bank', EstonianBank::$alias);
        $date = $request->query('date', date("Y-m-d", strtotime("yesterday")));

        $source = BankFactory::getBankOrDefault($bank);

        [[$result, $err], $from_cache] = $source->getJsonCurrenciesTableCached($date);

        if ($err) return response()->json([
            "error" => [
                "code" => "currencies.1",
                "message" =>
                    "Error occurred due to the wrong json structure. ".
                    "Most likely date is wrong or something happened with external source."
            ]
        ]);

        return response()->json([
            "cache" => $from_cache,
            "bank" => $bank,
            "data" => $result
        ]);
    }

    //TODO: add validation
    //TODO: improve error handling
    public function convert(Request $request, $currency = "USD"): JsonResponse
    {
        $target_currency = $request->query('to', "USD");
        $date            = $request->query('date', date("Y-m-d", strtotime("yesterday")));
        $bank            = $request->query('bank', EstonianBank::$alias);
        $amount          = $request->query('amount', 0);

        $source = BankFactory::getBankOrDefault($bank);

        [[$result, $err], $from_cache] = $source->convert(
            $currency,
            $target_currency,
            floatval($amount),
            $date);

        if ($err) return response()->json([
            "error" => [
                "code" => "convert.1",
                "message" =>
                    "Error occurred while processing currencies rates from the table ".
                    "Most likely one of the currencies is missing or has invalid alias."
            ]
        ]);

        return response()->json([
            "cache" => $from_cache,
            "bank" => $bank,
            "data" => [
                "converted" => [
                    "related" => $date,
                    "from" => $currency,
                    "amount" => floatval($amount),
                    "to" => $target_currency,
                    "result" => $result
                ]
            ]
        ]);
    }
}
