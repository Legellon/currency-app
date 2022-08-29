<?php

namespace App\Services\Banks;

class LithuanianBank extends Bank
{
    public static string $alias = "lithuanian_bank";

    public function getTable(): string
    {
        return "Lithuanian Bank Table";
    }
}
