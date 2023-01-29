<?php

use Illuminate\Support\Facades\Cache;
use Tests\Utils\Firestore\Faker\BookFaker;
use Tests\Utils\Firestore\Faker\UserFaker;

use function Pest\Laravel\deleteJson;
use function Pest\Laravel\postJson;
use function Pest\Laravel\putJson;

it('should be able to increment book counters when a book is added to user profile', function () {
    $user = UserFaker::factory()->create();

    $request = BookFaker::factory()->make();

    postJson(route('api.v1.users.books.store', ['user' => $user->id()]), $request);

    $bookCounter = Cache::get("user-{$user->id()}-books-counter");
    $pageCounter = Cache::get("user-{$user->id()}-pages-counter");

    expect($bookCounter)->toBe(1);
    expect($pageCounter)->toBe($request['number_of_pages']);
});

it('should be able to updates book counters when a book is added to user profile', function () {
    $user = UserFaker::factory()->create();

    $request = BookFaker::factory()->make();

    $initialBooksCounter = fake()->randomNumber(2);
    $initialPagesCounter = fake()->numberBetween(500, 1000);

    Cache::set("user-{$user->id()}-books-counter", $initialBooksCounter);
    Cache::set("user-{$user->id()}-pages-counter", $initialPagesCounter);

    postJson(route('api.v1.users.books.store', ['user' => $user->id()]), $request);

    $bookCounter = Cache::get("user-{$user->id()}-books-counter");
    $pageCounter = Cache::get("user-{$user->id()}-pages-counter");

    expect($bookCounter)->toBe($initialBooksCounter + 1);
    expect($pageCounter)->toBe($initialPagesCounter + $request['number_of_pages']);
});

it('should be able to updates book counters when a book is updated in user profile', function () {
    $user = UserFaker::factory()->create();
    $book = BookFaker::factory()
        ->user($user->id())
        ->create();

    $request = ['number_of_pages' => 200];

    $initialBooksCounter = fake()->randomNumber(2);
    $initialPagesCounter = fake()->numberBetween(500, 1000);

    Cache::set("user-{$user->id()}-books-counter", $initialBooksCounter);
    Cache::set("user-{$user->id()}-pages-counter", $initialPagesCounter);

    putJson(route('api.v1.users.books.update', ['user' => $user->id(), 'book' => $book->id()]), $request);

    $bookCounter = Cache::get("user-{$user->id()}-books-counter");
    $pageCounter = Cache::get("user-{$user->id()}-pages-counter");

    expect($bookCounter)->toBe($initialBooksCounter);
    expect($pageCounter)->toBe($initialPagesCounter - $book->data()['number_of_pages'] + $request['number_of_pages']);
});

it('should be able to decrement book counters when a book is deleted from user profile', function () {
    $user = UserFaker::factory()->create();
    $book = BookFaker::factory()
        ->user($user->id())
        ->create();

    $initialBooksCounter = fake()->randomNumber(2);
    $initialPagesCounter = fake()->numberBetween(500, 1000);

    Cache::set("user-{$user->id()}-books-counter", $initialBooksCounter);
    Cache::set("user-{$user->id()}-pages-counter", $initialPagesCounter);

    deleteJson(route('api.v1.users.books.destroy', ['user' => $user->id(), 'book' => $book->id()]));

    $bookCounter = Cache::get("user-{$user->id()}-books-counter");
    $pageCounter = Cache::get("user-{$user->id()}-pages-counter");

    expect($bookCounter)->toBe($initialBooksCounter - 1);
    expect($pageCounter)->toBe($initialPagesCounter - $book->data()['number_of_pages']);
});
