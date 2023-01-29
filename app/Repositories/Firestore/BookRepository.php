<?php

namespace App\Repositories\Firestore;

use App\Exceptions\DocumentNotFoundException;
use Google\Cloud\Firestore\CollectionReference;
use Google\Cloud\Firestore\DocumentReference;
use Google\Cloud\Firestore\DocumentSnapshot;
use Kreait\Firebase\Contract\Firestore;

class BookRepository
{
    protected CollectionReference $collection;

    public function __construct(protected Firestore $firestore)
    {
    }

    public function user(string $id)
    {
        $this->collection = $this->firestore
            ->database()
            ->collection('users')
            ->document($id)
            ->collection('books');

        return $this;
    }

    public function all()
    {
        return collect($this->collection->documents()->rows());
    }

    public function findById(string $id)
    {
        $user = $this->collection->document($id)->snapshot();

        if (!$user->exists()) {
            throw new DocumentNotFoundException();
        }

        return $user;
    }

    public function create(array $data): DocumentSnapshot
    {
        return $this->collection->add($data)->snapshot();
    }

    public function update(DocumentReference $document, array $data = [])
    {
        $convertedData = array_map(fn($key, $value) => ['path' => $key, 'value' => $value], array_keys($data), $data);

        $document->update($convertedData);

        return $document->snapshot();
    }
}
