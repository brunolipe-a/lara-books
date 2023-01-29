<?php

namespace Tests\Utils\Firestore;

use Kreait\Firebase\Contract\Firestore;
use Tests\Utils\Firestore\Faker\BookFaker;
use Tests\Utils\Firestore\Faker\UserFaker;

class FirestoreHelper
{
    public function __construct(protected Firestore $firestore)
    {
    }

    public function truncateDatabase()
    {
        $users = UserFaker::factory()
            ->collection()
            ->documents();

        foreach ($users as $user) {
            $books = BookFaker::factory()
                ->user($user->id())
                ->collection()
                ->documents();

            foreach ($books as $book) {
                $book->reference()->delete();
            }

            $user->reference()->delete();
        }
    }
}
