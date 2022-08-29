<?php

namespace App\Services\Banks;

abstract class Bank implements CurrencySource
{
    public static string $alias;
}
