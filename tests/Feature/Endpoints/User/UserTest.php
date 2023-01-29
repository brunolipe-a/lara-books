<?php

use Illuminate\Support\Facades\Cache;
use Tests\Utils\Firestore\Faker\UserFaker;

use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;
use function Pest\Laravel\putJson;

it('should be able to store a new user', function () {
    $request = UserFaker::factory()->make();

    postJson(route('api.v1.users.store'), $request)
        ->assertCreated()
        ->assertJsonStructure(['id', 'name', 'email', 'birthday', 'is_active']);

    $documents = UserFaker::factory()
        ->collection()
        ->where('email', '=', $request['email'])
        ->where('is_active', '=', true)
        ->documents();

    expect($documents->isEmpty())->toBeFalse();
});

it('should be able to store a new user with hashed password', function () {
    $request = UserFaker::factory()->make();

    postJson(route('api.v1.users.store'), $request)->assertCreated();

    $documents = UserFaker::factory()
        ->collection()
        ->where('email', '=', $request['email'])
        ->where('password', '=', $request['password'])
        ->documents();

    expect($documents->isEmpty())->toBeTrue();
});

it('should be able to list all users', function () {
    $count = 3;

    UserFaker::factory()->createMany($count);

    getJson(route('api.v1.users.index'))
        ->assertOk()
        ->assertJsonCount($count)
        ->assertJsonStructure([['id', 'name', 'email', 'birthday', 'is_active']]);
});

it('should be able to show an user', function () {
    $user = UserFaker::factory()->create();

    getJson(route('api.v1.users.show', ['user' => $user->id()]))
        ->assertOk()
        ->assertJsonStructure(['id', 'name', 'email', 'birthday', 'is_active']);
});

it('should be able to update an user', function () {
    $user = UserFaker::factory()->create();

    $request = UserFaker::factory()->make();

    putJson(route('api.v1.users.update', ['user' => $user->id()]), $request)
        ->assertOk()
        ->assertJsonStructure(['id', 'name', 'email', 'birthday', 'is_active']);

    $documents = UserFaker::factory()
        ->collection()
        ->documents();

    expect($documents->size())->toEqual(1);

    $documents = UserFaker::factory()
        ->collection()
        ->where('email', '=', $request['email'])
        ->documents();

    expect($documents->isEmpty())->toBeFalse();
});

it('should be able to update an user without some fields', function () {
    $user = UserFaker::factory()->create();

    $request = ['name' => 'Bruno', 'is_active' => false];

    putJson(route('api.v1.users.update', ['user' => $user->id()]), $request)
        ->assertOk()
        ->assertJsonStructure(['id', 'name', 'email', 'birthday', 'is_active']);

    $documents = UserFaker::factory()
        ->collection()
        ->documents();

    expect($documents->size())->toEqual(1);

    $documents = UserFaker::factory()
        ->collection()
        ->where('email', '=', $user->data()['email'])
        ->where('name', '=', $request['name'])
        ->where('is_active', '=', $request['is_active'])
        ->documents();

    expect($documents->isEmpty())->toBeFalse();
});

it('should be able to delete an user', function () {
    $user = UserFaker::factory()->create();

    deleteJson(route('api.v1.users.destroy', ['user' => $user->id()]))->assertNoContent();

    $documents = UserFaker::factory()
        ->collection()
        ->documents();

    expect($documents->isEmpty())->toBeTrue();
});

it('should be able to delete books counter when delete an user', function () {
    $user = UserFaker::factory()->create();

    Cache::set("user-{$user->id()}-books-counter", 1);
    Cache::set("user-{$user->id()}-pages-counter", 100);

    deleteJson(route('api.v1.users.destroy', ['user' => $user->id()]))->assertNoContent();

    $documents = UserFaker::factory()
        ->collection()
        ->documents();

    expect($documents->isEmpty())->toBeTrue();

    expect(Cache::get("user-{$user->id()}-books-counter"))->toBeNull();
    expect(Cache::get("user-{$user->id()}-pages-counter"))->toBeNull();
});
