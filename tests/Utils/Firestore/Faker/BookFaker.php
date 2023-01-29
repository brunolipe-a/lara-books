<?php

namespace Tests\Utils\Firestore\Faker;

use Google\Cloud\Firestore\CollectionReference;

class BookFaker extends FirestoreFaker
{
    const COLLECTION = 'books';

    protected string $userId;

    public static function factory()
    {
        return new self(app('firebase.firestore'));
    }

    protected function definition(): array
    {
        return [
            'name' => fake()->sentence(3),
            'category' => fake()->word(),
            'author' => fake()->name(),
            'year' => fake()->year(),
            'number_of_pages' => fake()->numberBetween(100, 400),
            'language' => fake()->word(),
            'edition' => fake()->randomNumber(),
            'publisher' => [
                'name' => fake()->company(),
                'code' => fake()->word(),
                'phone' => fake()->phoneNumber(),
            ],
            'isbn' => fake()->word(),
        ];
    }

    public function collection(): CollectionReference
    {
        return $this->firestore
            ->database()
            ->collection(UserFaker::COLLECTION)
            ->document($this->userId)
            ->collection(self::COLLECTION);
    }

    public function user(string $userId)
    {
        $this->userId = $userId;

        return $this;
    }

    public function make(array $data = []): array
    {
        return array_merge($this->definition(), $data);
    }

    public function create(array $data = [])
    {
        return $this->collection()
            ->add($this->make($data))
            ->snapshot();
    }

    public function createMany(int $count)
    {
        for ($i = 0; $i < $count; $i++) {
            $this->create();
        }
    }
}
