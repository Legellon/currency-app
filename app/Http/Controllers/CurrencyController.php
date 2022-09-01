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

        $source = BankFactory::getBankOrDefault($bank_alias);

        return response()->json([
            "data" => $source->getJsonCurrencyTable()
        ]);
    }
}
