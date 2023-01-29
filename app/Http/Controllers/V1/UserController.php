<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Repositories\Firestore\UserRepository;
use Illuminate\Support\Facades\Cache;

class UserController extends Controller
{
    public function __construct(protected UserRepository $userRepository)
    {
    }

    public function index()
    {
        return UserResource::collection($this->userRepository->all());
    }

    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();

        $user = $this->userRepository->create($data);

        return response()->json(new UserResource($user), 201);
    }

    public function show($id)
    {
        $user = $this->userRepository->findById($id);

        return new UserResource($user);
    }

    public function update(UpdateUserRequest $request, $id)
    {
        $data = $request->validated();

        $user = $this->userRepository->findById($id);

        $updatedUser = $this->userRepository->update($user->reference(), $data);

        return new UserResource($updatedUser);
    }

    public function destroy($id)
    {
        $user = $this->userRepository->findById($id);

        $user->reference()->delete();

        Cache::deleteMultiple(["user-{$user->id()}-books-counter", "user-{$user->id()}-pages-counter"]);

        return response()->noContent();
    }
}
