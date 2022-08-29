<?php

namespace App\Services\Banks;

abstract class Bank implements CurrencySource
{
    use HasAlias;
}
