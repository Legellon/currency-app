<?php

namespace App\Services\Banks;

interface CurrencySource
{
    public function getTable(): string;
}
