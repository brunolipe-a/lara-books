<?php

use Tests\Utils\Firestore\Faker\UserFaker;
use Tests\Utils\Firestore\FirestoreHelper;

use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;
use function Pest\Laravel\putJson;

beforeEach(function () {
    $this->firestore = app('firebase.firestore');

    $this->helper = FirestoreHelper::new($this->firestore);

    $this->helper->deleteCollection('users');
});

it('should be able to store a new user', function () {
    $request = UserFaker::make();

    postJson(route('api.v1.users.store'), $request)
        ->assertCreated()
        ->assertJsonStructure(['id', 'name', 'email', 'birthday', 'is_active']);

    $documents = $this->firestore
        ->database()
        ->collection('users')
        ->where('email', '=', $request['email'])
        ->where('is_active', '=', true)
        ->documents();

    expect($documents->isEmpty())->toBeFalse();
});

it('should be able to store a new user with hashed password', function () {
    $request = UserFaker::make();

    postJson(route('api.v1.users.store'), $request)->assertCreated();

    $documents = $this->firestore
        ->database()
        ->collection('users')
        ->where('email', '=', $request['email'])
        ->where('password', '=', $request['password'])
        ->documents();

    expect($documents->isEmpty())->toBeTrue();
});

it('should be able to list all users', function () {
    $count = 3;

    $this->helper->createMany(UserFaker::class, $count);

    getJson(route('api.v1.users.index'))
        ->assertOk()
        ->assertJsonCount($count)
        ->assertJsonStructure([['id', 'name', 'email', 'birthday', 'is_active']]);
});

it('should be able to show an user', function () {
    $user = $this->helper->create(UserFaker::class);

    getJson(route('api.v1.users.show', ['user' => $user->id()]))
        ->assertOk()
        ->assertJsonStructure(['id', 'name', 'email', 'birthday', 'is_active']);
});

it('should be able to update an user', function () {
    $user = $this->helper->create(UserFaker::class);

    $request = UserFaker::make();

    putJson(route('api.v1.users.update', ['user' => $user->id()]), $request)
        ->assertOk()
        ->assertJsonStructure(['id', 'name', 'email', 'birthday', 'is_active']);

    $documents = $this->firestore
        ->database()
        ->collection('users')
        ->documents();

    expect($documents->size())->toEqual(1);

    $documents = $this->firestore
        ->database()
        ->collection('users')
        ->where('email', '=', $request['email'])
        ->documents();

    expect($documents->isEmpty())->toBeFalse();
});

it('should be able to update an user without some fields', function () {
    $user = $this->helper->create(UserFaker::class);

    $request = ['name' => 'Bruno', 'is_active' => false];

    putJson(route('api.v1.users.update', ['user' => $user->id()]), $request)
        ->assertOk()
        ->assertJsonStructure(['id', 'name', 'email', 'birthday', 'is_active']);

    $documents = $this->firestore
        ->database()
        ->collection('users')
        ->documents();

    expect($documents->size())->toEqual(1);

    $documents = $this->firestore
        ->database()
        ->collection('users')
        ->where('email', '=', $user->data()['email'])
        ->where('name', '=', $request['name'])
        ->where('is_active', '=', $request['is_active'])
        ->documents();

    expect($documents->isEmpty())->toBeFalse();
});

it('should be able to delete an user', function () {
    $user = $this->helper->create(UserFaker::class);

    deleteJson(route('api.v1.users.destroy', ['user' => $user->id()]))->assertNoContent();

    $documents = $this->firestore
        ->database()
        ->collection('users')
        ->documents();

    expect($documents->isEmpty())->toBeTrue();
});
