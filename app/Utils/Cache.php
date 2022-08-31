<?php

namespace App\Utils\Cache;

use Illuminate\Support\Facades\Redis;

function getCachedOrCacheJsonFromRedis(string $key, int $ex, $getDataFromSrc): array
{
    $data = Redis::get($key);

    if (is_null($data))
    {
        $data = $getDataFromSrc();
        Redis::set($key, json_encode($data), 'EX', $ex);
    }
    else
    {
        $data = json_decode($data, true);
    }

    return $data;
}
