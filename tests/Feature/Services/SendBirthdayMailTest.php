<?php

use App\Mail\UserBirthday;
use App\Services\SendBirthdayMailService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Tests\Utils\Firestore\Faker\UserFaker;

it('should be able to return only user on birthday', function () {
    Mail::fake();

    $user = UserFaker::factory()->create();

    $booksCounter = fake()->randomNumber(2);
    $pagesCounter = fake()->numberBetween(500, 1000);

    Cache::set("user-{$user->id()}-books-counter", $booksCounter);
    Cache::set("user-{$user->id()}-pages-counter", $pagesCounter);

    $userData = $user->data();

    $service = app(SendBirthdayMailService::class);

    $service->handle($user);

    Mail::assertSent(
        UserBirthday::class,
        fn(UserBirthday $mail) => $mail->hasTo($userData['email']) &&
            $mail->userData['books_counter'] === $booksCounter &&
            $mail->userData['pages_counter'] === $pagesCounter,
    );

    expect(Cache::get("user-{$user->id()}-books-counter", 0))->toBe(0);
    expect(Cache::get("user-{$user->id()}-pages-counter", 0))->toBe(0);
});
