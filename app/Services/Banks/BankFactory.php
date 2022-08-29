<?php

namespace App\Services\Banks;

class BankFactory
{
    public static function getBankInstance(string $bankAlias): Bank
    {
        return match ($bankAlias) {
            EstonianBank::$alias => new EstonianBank(),
            LithuanianBank::$alias => new LithuanianBank(),
        };
    }
}
