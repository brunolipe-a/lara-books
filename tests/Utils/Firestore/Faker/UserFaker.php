<?php

namespace Tests\Utils\Firestore\Faker;

use Google\Cloud\Firestore\CollectionReference;

class UserFaker extends FirestoreFaker
{
    const COLLECTION = 'users';

    public static function factory()
    {
        return new self(app('firebase.firestore'));
    }

    protected function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()
                ->unique()
                ->email(),
            'password' => fake()->password(),
            'birthday' => fake()->date(max: now()->subDay()),
            'is_active' => true,
        ];
    }

    public function collection(): CollectionReference
    {
        return $this->firestore->database()->collection(self::COLLECTION);
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
