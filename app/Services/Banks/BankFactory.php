<?php

namespace App\Services\Banks;

class BankFactory
{
    public static function getBankOrDefault(string $bankAlias): Bank
    {
        return match ($bankAlias)
        {
            LithuanianBank::$alias => new LithuanianBank(),
            default => new EstonianBank(),
        };
    }
}
