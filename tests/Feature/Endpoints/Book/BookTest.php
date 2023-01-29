<?php

use Tests\Utils\Firestore\Faker\BookFaker;
use Tests\Utils\Firestore\Faker\UserFaker;

use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;
use function Pest\Laravel\putJson;

it('should be able to store a new book', function () {
    $user = UserFaker::factory()->create();

    $request = BookFaker::factory()->make();

    postJson(route('api.v1.users.books.store', ['user' => $user->id()]), $request)
        ->assertCreated()
        ->assertJsonStructure([
            'id',
            'name',
            'category',
            'author',
            'year',
            'number_of_pages',
            'language',
            'edition',
            'publisher' => ['name', 'code', 'phone'],
            'isbn',
        ]);

    $documents = BookFaker::factory()
        ->user($user->id())
        ->collection()
        ->where('name', '=', $request['name'])
        ->documents();

    expect($documents->isEmpty())->toBeFalse();
});

it('should be able to list all users', function () {
    $count = 3;

    $user = UserFaker::factory()->create();

    BookFaker::factory()
        ->user($user->id())
        ->createMany($count);

    getJson(route('api.v1.users.books.index', ['user' => $user->id()]))
        ->assertOk()
        ->assertJsonCount($count)
        ->assertJsonStructure([
            [
                'id',
                'name',
                'category',
                'author',
                'year',
                'number_of_pages',
                'language',
                'edition',
                'publisher' => ['name', 'code', 'phone'],
                'isbn',
            ],
        ]);
});

it('should be able to show a book', function () {
    $user = UserFaker::factory()->create();

    $book = BookFaker::factory()
        ->user($user->id())
        ->create();

    getJson(route('api.v1.users.books.show', ['user' => $user->id(), 'book' => $book->id()]))
        ->assertOk()
        ->assertJsonStructure([
            'id',
            'name',
            'category',
            'author',
            'year',
            'number_of_pages',
            'language',
            'edition',
            'publisher' => ['name', 'code', 'phone'],
            'isbn',
        ]);
});

it('should be able to update a book', function () {
    $user = UserFaker::factory()->create();

    $book = BookFaker::factory()
        ->user($user->id())
        ->create();

    $request = BookFaker::factory()->make();

    putJson(route('api.v1.users.books.update', ['user' => $user->id(), 'book' => $book->id()]), $request)
        ->assertOk()
        ->assertJsonStructure([
            'id',
            'name',
            'category',
            'author',
            'year',
            'number_of_pages',
            'language',
            'edition',
            'publisher' => ['name', 'code', 'phone'],
            'isbn',
        ]);

    $documents = BookFaker::factory()
        ->user($user->id())
        ->collection()
        ->documents();

    expect($documents->size())->toEqual(1);

    $documents = BookFaker::factory()
        ->user($user->id())
        ->collection()
        ->where('name', '=', $request['name'])
        ->documents();

    expect($documents->isEmpty())->toBeFalse();
});

it('should be able to update a book without some fields', function () {
    $user = UserFaker::factory()->create();

    $book = BookFaker::factory()
        ->user($user->id())
        ->create();

    $request = ['name' => 'O livro'];

    putJson(route('api.v1.users.books.update', ['user' => $user->id(), 'book' => $book->id()]), $request)->assertOk();

    $documents = BookFaker::factory()
        ->user($user->id())
        ->collection()
        ->documents();

    expect($documents->size())->toEqual(1);

    $documents = BookFaker::factory()
        ->user($user->id())
        ->collection()
        ->where('name', '=', $request['name'])
        ->where('author', '=', $book->data()['author'])
        ->documents();

    expect($documents->isEmpty())->toBeFalse();
});

it('should be able to delete an user', function () {
    $user = UserFaker::factory()->create();

    $book = BookFaker::factory()
        ->user($user->id())
        ->create();

    deleteJson(route('api.v1.users.books.destroy', ['user' => $user->id(), 'book' => $book->id()]))->assertNoContent();

    $documents = BookFaker::factory()
        ->user($user->id())
        ->collection()
        ->documents();

    expect($documents->isEmpty())->toBeTrue();
});
