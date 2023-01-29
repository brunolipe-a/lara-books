<?php

namespace Tests\Utils\Firestore\Faker;

abstract class FirestoreFaker
{
    /** @var string */
    const COLLECTION = self::COLLECTION;

    abstract public static function make(array $mergeData = []): array;
}
