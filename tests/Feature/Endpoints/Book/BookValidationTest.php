<?php

use Tests\Utils\Firestore\Faker\BookFaker;
use Tests\Utils\Firestore\Faker\UserFaker;

use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;
use function Pest\Laravel\putJson;

it('should not be able to store a new book with invalid userId', function () {
    $request = BookFaker::factory()->make();

    postJson(route('api.v1.users.books.store', ['user' => 'invalid']), $request)->assertNotFound();
});

it('should not be able to list books with invalid userId', function () {
    getJson(route('api.v1.users.books.index', ['user' => 'invalid']))->assertNotFound();
});

it('should not be able to show books with invalid userId', function () {
    $user = UserFaker::factory()->create();
    $book = BookFaker::factory()
        ->user($user->id())
        ->create();

    getJson(route('api.v1.users.books.show', ['user' => 'invalid', 'book' => $book->id()]))->assertNotFound();
});

it('should not be able to show books with invalid bookId', function () {
    $user = UserFaker::factory()->create();

    BookFaker::factory()
        ->user($user->id())
        ->create();

    getJson(route('api.v1.users.books.show', ['user' => $user->id(), 'book' => 'invalid']))->assertNotFound();
});

it('should not be able to store a new book without required data', function () {
    $user = UserFaker::factory()->create();

    postJson(route('api.v1.users.books.store', ['user' => $user->id()]))->assertInvalid([
        'name',
        'category',
        'author',
        'year',
        'number_of_pages',
        'language',
        'edition',
        'publisher',
        'isbn',
    ]);
});

it('should not be able to show an book with invalid userId', function () {
    $user = UserFaker::factory()->create();
    $book = BookFaker::factory()
        ->user($user->id())
        ->create();

    getJson(route('api.v1.users.books.show', ['user' => 'invalid', 'book' => $book->id()]))->assertNotFound();
});

it('should not be able to show an book with invalid bookId', function () {
    $user = UserFaker::factory()->create();
    BookFaker::factory()
        ->user($user->id())
        ->create();

    getJson(route('api.v1.users.books.show', ['user' => $user->id(), 'book' => 'invalid']))->assertNotFound();
});

it('should not be able to update an book with invalid userId', function () {
    $user = UserFaker::factory()->create();
    $book = BookFaker::factory()
        ->user($user->id())
        ->create();

    putJson(route('api.v1.users.books.update', ['user' => 'invalid', 'book' => $book->id()]))->assertNotFound();
});

it('should not be able to update an book with invalid bookId', function () {
    $user = UserFaker::factory()->create();
    BookFaker::factory()
        ->user($user->id())
        ->create();

    putJson(route('api.v1.users.books.update', ['user' => $user->id(), 'book' => 'invalid']))->assertNotFound();
});

it('should not be able to destroy an book with invalid userId', function () {
    $user = UserFaker::factory()->create();
    $book = BookFaker::factory()
        ->user($user->id())
        ->create();

    deleteJson(route('api.v1.users.books.destroy', ['user' => 'invalid', 'book' => $book->id()]))->assertNotFound();
});

it('should not be able to destroy an book with invalid bookId', function () {
    $user = UserFaker::factory()->create();
    BookFaker::factory()
        ->user($user->id())
        ->create();

    deleteJson(route('api.v1.users.books.destroy', ['user' => $user->id(), 'book' => 'invalid']))->assertNotFound();
});
