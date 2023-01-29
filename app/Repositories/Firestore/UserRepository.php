<?php

namespace App\Repositories\Firestore;

use App\Exceptions\AppException;
use App\Exceptions\DocumentNotFoundException;
use Google\Cloud\Firestore\CollectionReference;
use Google\Cloud\Firestore\DocumentReference;
use Google\Cloud\Firestore\DocumentSnapshot;
use Illuminate\Support\Facades\Hash;
use Kreait\Firebase\Contract\Firestore;

class UserRepository
{
    protected CollectionReference $collection;

    public function __construct(protected Firestore $firestore)
    {
        $this->collection = $this->firestore->database()->collection('users');
    }

    public function query()
    {
        return $this->collection;
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
        $this->validateEmail($data['email']);

        $data['password'] = Hash::make($data['password']);
        $data['is_active'] = true;

        return $this->collection->add($data)->snapshot();
    }

    public function update(DocumentReference $document, array $data = [])
    {
        if (isset($data['email']) && $document->snapshot()->data()['email'] !== $data['email']) {
            $this->validateEmail($data['email']);
        }

        $convertedData = array_map(fn($key, $value) => ['path' => $key, 'value' => $value], array_keys($data), $data);

        $document->update($convertedData);

        return $document->snapshot();
    }

    protected function validateEmail(string $email)
    {
        $documentsWithThatEmail = $this->collection->where('email', '=', $email)->documents();

        if (!$documentsWithThatEmail->isEmpty()) {
            throw new AppException('E-mail já cadastrado');
        }
    }
}
