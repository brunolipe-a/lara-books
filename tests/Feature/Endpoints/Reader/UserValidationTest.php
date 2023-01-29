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

it('should not be able to store a new user with duplicated email', function () {
    $userData = [
        'email' => 'bruno@test.com',
    ];

    $this->firestore
        ->database()
        ->collection('users')
        ->add($userData);

    $request = UserFaker::make($userData);

    postJson(route('api.v1.users.store'), $request)->assertStatus(400);

    $documents = $this->firestore
        ->database()
        ->collection('users')
        ->where('email', '=', $request['email'])
        ->documents();

    expect($documents->size())->toEqual(1);
});

it('should not be able to store a new user without required data', function () {
    postJson(route('api.v1.users.store'))->assertInvalid(['name', 'email', 'password', 'birthday']);

    $documents = $this->firestore
        ->database()
        ->collection('users')
        ->documents();

    expect($documents->isEmpty())->toBeTrue();
});

it('should not be able to store a new user with invalid email', function () {
    postJson(route('api.v1.users.store'), ['email' => 'invalid'])->assertInvalid(['email']);

    $documents = $this->firestore
        ->database()
        ->collection('users')
        ->documents();

    expect($documents->isEmpty())->toBeTrue();
});

it('should not be able to store a new user with invalid birthday', function () {
    postJson(route('api.v1.users.store'), ['birthday' => 'invalid'])->assertInvalid(['birthday']);

    $documents = $this->firestore
        ->database()
        ->collection('users')
        ->documents();

    expect($documents->isEmpty())->toBeTrue();
});

it('should not be able to show an user with invalid id', function () {
    getJson(route('api.v1.users.show', ['user' => 'invalid']))->assertNotFound();
});

it('should not be able to update an user with invalid id', function () {
    putJson(route('api.v1.users.update', ['user' => 'invalid']))->assertNotFound();
});

it('should not be able to delete an user with invalid id', function () {
    deleteJson(route('api.v1.users.update', ['user' => 'invalid']))->assertNotFound();
});

it('should not be able to update an user with duplicated email', function () {
    $user = $this->helper->create(UserFaker::class);
    $secondUser = $this->helper->create(UserFaker::class);

    $request = UserFaker::make(['email' => $secondUser->data()['email']]);

    putJson(route('api.v1.users.update', ['user' => $user->id()]), $request)->assertStatus(400);

    $documents = $this->firestore
        ->database()
        ->collection('users')
        ->where('email', '=', $request['email'])
        ->documents();

    expect($documents->size())->toEqual(1);
});

it('should not be able to update an user with invalid email', function () {
    $user = $this->helper->create(UserFaker::class);

    putJson(route('api.v1.users.update', ['user' => $user->id()]), ['email' => 'invalid'])->assertInvalid(['email']);
});

it('should not be able to update an user with invalid birthday', function () {
    $user = $this->helper->create(UserFaker::class);

    putJson(route('api.v1.users.update', ['user' => $user->id()]), ['birthday' => 'invalid'])->assertInvalid([
        'birthday',
    ]);
});
