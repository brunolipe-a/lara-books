<?php

namespace Tests\Utils\Firestore;

use Kreait\Firebase\Contract\Firestore;

class FirestoreHelper
{
    public static function new(Firestore $firestore)
    {
        return new self($firestore);
    }

    public function __construct(protected Firestore $firestore)
    {
    }

    public function deleteCollection(string $name)
    {
        $documents = $this->firestore
            ->database()
            ->collection($name)
            ->documents();

        foreach ($documents as $document) {
            $document->reference()->delete();
        }
    }

    public function createMany(string $faker, int $count)
    {
        for ($i = 0; $i < $count; $i++) {
            $this->firestore
                ->database()
                ->collection($faker::COLLECTION)
                ->add($faker::make());
        }
    }

    public function create(string $faker)
    {
        return $this->firestore
            ->database()
            ->collection($faker::COLLECTION)
            ->add($faker::make())
            ->snapshot();
    }
}
