<?php

namespace App\Http\Controllers;

use App\Services\Banks\BankFactory;
use App\Services\Banks\CurrencySource;
use App\Services\Banks\EstonianBank;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class CurrencyController extends Controller
{
    protected CurrencySource $source;

    public function getTable(Request $request): string
    {
        $bankAlias = $request->query('bank', EstonianBank::$alias);

        $this->setCurrencySource(
            BankFactory::getBankInstance($bankAlias));

        return $this->source->getTable();
    }

    private function setCurrencySource(CurrencySource $source) {
        $this->source = $source;
    }
}
