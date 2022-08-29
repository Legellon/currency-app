<?php

namespace App\Services\Banks;

class EstonianBank extends Bank
{
    public static string $alias = "estonian_bank";

    public function getTable(): string
    {
        return "Estonian Bank Table";
    }
}
