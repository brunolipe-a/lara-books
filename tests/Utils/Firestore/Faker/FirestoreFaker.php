<?php

namespace Tests\Utils\Firestore\Faker;

use Google\Cloud\Firestore\CollectionReference;
use Google\Cloud\Firestore\DocumentSnapshot;
use Kreait\Firebase\Contract\Firestore;

abstract class FirestoreFaker
{
    /** @var string */
    const COLLECTION = self::COLLECTION;

    public function __construct(protected Firestore $firestore)
    {
    }

    abstract protected function definition(): array;

    abstract public function collection(): CollectionReference;

    abstract public function make(array $mergeData = []): array;

    abstract public function create(array $mergeData = []);

    abstract public function createMany(int $count);
}
