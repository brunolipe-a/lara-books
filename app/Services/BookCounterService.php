<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class BookCounterService
{
    public function increment(string $userId, int $numberOfPages)
    {
        $pagesCounter = Cache::get("user-{$userId}-pages-counter", 0);

        Cache::increment("user-{$userId}-books-counter");

        Cache::set("user-{$userId}-pages-counter", $pagesCounter + $numberOfPages);
    }

    public function decrement(string $userId, int $numberOfPages)
    {
        $pagesCounter = Cache::get("user-{$userId}-pages-counter", 0);

        Cache::decrement("user-{$userId}-books-counter");

        Cache::set("user-{$userId}-pages-counter", $pagesCounter - $numberOfPages);
    }

    public function update(string $userId, int $oldNumberOfPages, int $newNumberOfPages)
    {
        $pagesCounter = Cache::get("user-{$userId}-pages-counter", 0);

        Cache::set("user-{$userId}-pages-counter", $pagesCounter - $oldNumberOfPages + $newNumberOfPages);
    }
}
