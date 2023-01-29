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

it('should not be able to store a new reader profile with duplicated email', function () {
    $readerData = [
        'email' => 'bruno@test.com',
    ];

    $this->firestore
        ->database()
        ->collection('readers')
        ->add($readerData);

    $request = ReaderFaker::make($readerData);

    postJson(route('api.v1.readers.store'), $request)->assertStatus(400);

    $documents = $this->firestore
        ->database()
        ->collection('readers')
        ->where('email', '=', $request['email'])
        ->documents();

    expect($documents->size())->toEqual(1);
});

it('should not be able to store a new reader profile without required data', function () {
    postJson(route('api.v1.readers.store'))->assertInvalid(['name', 'email', 'password', 'birthday']);

    $documents = $this->firestore
        ->database()
        ->collection('readers')
        ->documents();

    expect($documents->isEmpty())->toBeTrue();
});

it('should not be able to store a new reader profile with invalid email', function () {
    postJson(route('api.v1.readers.store'), ['email' => 'invalid'])->assertInvalid(['email']);

    $documents = $this->firestore
        ->database()
        ->collection('readers')
        ->documents();

    expect($documents->isEmpty())->toBeTrue();
});

it('should not be able to store a new reader profile with invalid birthday', function () {
    postJson(route('api.v1.readers.store'), ['birthday' => 'invalid'])->assertInvalid(['birthday']);

    $documents = $this->firestore
        ->database()
        ->collection('readers')
        ->documents();

    expect($documents->isEmpty())->toBeTrue();
});

it('should not be able to show a reader profile with invalid id', function () {
    getJson(route('api.v1.readers.show', ['reader' => 'invalid']))->assertNotFound();
});

it('should not be able to update a reader profile with invalid id', function () {
    putJson(route('api.v1.readers.update', ['reader' => 'invalid']))->assertNotFound();
});

it('should not be able to delete a reader profile with invalid id', function () {
    deleteJson(route('api.v1.readers.update', ['reader' => 'invalid']))->assertNotFound();
});

it('should not be able to update a reader profile with duplicated email', function () {
    $reader = $this->helper->create(ReaderFaker::class);
    $secondReader = $this->helper->create(ReaderFaker::class);

    $request = ReaderFaker::make(['email' => $secondReader->data()['email']]);

    putJson(route('api.v1.readers.update', ['reader' => $reader->id()]), $request)->assertStatus(400);

    $documents = $this->firestore
        ->database()
        ->collection('readers')
        ->where('email', '=', $request['email'])
        ->documents();

    expect($documents->size())->toEqual(1);
});

it('should not be able to update a reader profile with invalid email', function () {
    $reader = $this->helper->create(ReaderFaker::class);

    putJson(route('api.v1.readers.update', ['reader' => $reader->id()]), ['email' => 'invalid'])->assertInvalid([
        'email',
    ]);
});

it('should not be able to update a reader profile with invalid birthday', function () {
    $reader = $this->helper->create(ReaderFaker::class);

    putJson(route('api.v1.readers.update', ['reader' => $reader->id()]), ['birthday' => 'invalid'])->assertInvalid([
        'birthday',
    ]);
});
