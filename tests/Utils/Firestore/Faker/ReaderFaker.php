<?php

namespace Tests\Utils\Firestore\Faker;

class ReaderFaker extends FirestoreFaker
{
    const COLLECTION = 'readers';

    public static function make(array $mergeData = []): array
    {
        $initialData = [
            'name' => fake()->name(),
            'email' => fake()->email(),
            'password' => fake()->password(),
            'birthday' => fake()->date(),
            'is_active' => true,
        ];

        return array_merge($initialData, $mergeData);
    }
}
