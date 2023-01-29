<?php

use Tests\Utils\Firestore\Faker\ReaderFaker;
use Tests\Utils\Firestore\FirestoreHelper;

use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;
use function Pest\Laravel\putJson;

beforeEach(function () {
    $this->firestore = app('firebase.firestore');

    $this->helper = FirestoreHelper::new($this->firestore);

    $this->helper->deleteCollection('readers');
});

it('should be able to store a new reader profile', function () {
    $request = ReaderFaker::make();

    postJson(route('api.v1.readers.store'), $request)
        ->assertCreated()
        ->assertJsonStructure(['id', 'name', 'email', 'birthday', 'is_active']);

    $documents = $this->firestore
        ->database()
        ->collection('readers')
        ->where('email', '=', $request['email'])
        ->where('is_active', '=', true)
        ->documents();

    expect($documents->isEmpty())->toBeFalse();
});

it('should be able to store a new reader profile with hashed password', function () {
    $request = ReaderFaker::make();

    postJson(route('api.v1.readers.store'), $request)->assertCreated();

    $documents = $this->firestore
        ->database()
        ->collection('readers')
        ->where('email', '=', $request['email'])
        ->where('password', '=', $request['password'])
        ->documents();

    expect($documents->isEmpty())->toBeTrue();
});

it('should be able to list all readers profile', function () {
    $count = 3;

    $this->helper->createMany(ReaderFaker::class, $count);

    getJson(route('api.v1.readers.index'))
        ->assertOk()
        ->assertJsonCount($count)
        ->assertJsonStructure([['id', 'name', 'email', 'birthday', 'is_active']]);
});

it('should be able to show a reader profile', function () {
    $reader = $this->helper->create(ReaderFaker::class);

    getJson(route('api.v1.readers.show', ['reader' => $reader->id()]))
        ->assertOk()
        ->assertJsonStructure(['id', 'name', 'email', 'birthday', 'is_active']);
});

it('should be able to update a reader profile', function () {
    $reader = $this->helper->create(ReaderFaker::class);

    $request = ReaderFaker::make();

    putJson(route('api.v1.readers.update', ['reader' => $reader->id()]), $request)
        ->assertOk()
        ->assertJsonStructure(['id', 'name', 'email', 'birthday', 'is_active']);

    $documents = $this->firestore
        ->database()
        ->collection('readers')
        ->documents();

    expect($documents->size())->toEqual(1);

    $documents = $this->firestore
        ->database()
        ->collection('readers')
        ->where('email', '=', $request['email'])
        ->documents();

    expect($documents->isEmpty())->toBeFalse();
});

it('should be able to update a reader profile without some fields', function () {
    $reader = $this->helper->create(ReaderFaker::class);

    $request = ['name' => 'Bruno', 'is_active' => false];

    putJson(route('api.v1.readers.update', ['reader' => $reader->id()]), $request)
        ->assertOk()
        ->assertJsonStructure(['id', 'name', 'email', 'birthday', 'is_active']);

    $documents = $this->firestore
        ->database()
        ->collection('readers')
        ->documents();

    expect($documents->size())->toEqual(1);

    $documents = $this->firestore
        ->database()
        ->collection('readers')
        ->where('email', '=', $reader->data()['email'])
        ->where('name', '=', $request['name'])
        ->where('is_active', '=', $request['is_active'])
        ->documents();

    expect($documents->isEmpty())->toBeFalse();
});

it('should be able to delete a reader profile', function () {
    $reader = $this->helper->create(ReaderFaker::class);

    deleteJson(route('api.v1.readers.destroy', ['reader' => $reader->id()]))->assertNoContent();

    $documents = $this->firestore
        ->database()
        ->collection('readers')
        ->documents();

    expect($documents->isEmpty())->toBeTrue();
});
