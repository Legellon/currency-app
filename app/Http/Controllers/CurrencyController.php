<?php

namespace App\Http\Controllers;

use App\Services\Banks\BankFactory;
use App\Services\Banks\EstonianBank;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class CurrencyController extends Controller
{
    public function getTable(Request $request): JsonResponse
    {
        $bank_alias = $request->query('bank', EstonianBank::$alias);

        $source = BankFactory::getBankOrDefault($bank_alias);

        return response()->json([
            "data" => $source->getJsonCurrencyTable()
        ]);
    }
}
