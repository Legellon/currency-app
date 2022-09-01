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
    public function getTable(Request $request): JsonResponse
    {
        $bank = $request->query('bank', EstonianBank::$alias);
        $date = $request->query('date', date("Y-m-d", strtotime("yesterday")));

        $source = BankFactory::getBankOrDefault($bank);

        [$result, $error] = $source->getJsonCurrencyTable($date);

        if ($error) {
            return response()->json([
                "error" => [
                    "code" => "currencies.1",
                    "message" =>
                        "Error occurred due to the wrong json structure.".
                        "Most likely date is wrong or something happened with external source."
                ],
            ]);
        }

        return response()->json([
            "data" => $result
        ]);
    }
}
