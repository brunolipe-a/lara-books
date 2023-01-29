<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Http\Resources\BookResource;
use App\Repositories\Firestore\BookRepository;
use App\Repositories\Firestore\UserRepository;
use App\Services\BookCounterService;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function __construct(
        protected BookRepository $bookRepository,
        protected UserRepository $userRepository,
        protected BookCounterService $bookCounterService,
    ) {
    }

    public function index(string $userId)
    {
        $user = $this->userRepository->findById($userId);

        $books = $this->bookRepository->user($user->id())->all();

        return BookResource::collection($books);
    }

    public function store(StoreBookRequest $request, string $userId)
    {
        $user = $this->userRepository->findById($userId);

        $data = $request->validated();

        $book = $this->bookRepository->user($user->id())->create($data);

        $this->bookCounterService->increment($user->id(), $request->get('number_of_pages'));

        return response()->json(new BookResource($book), 201);
    }

    public function show(string $userId, string $id)
    {
        $book = $this->bookRepository->user($userId)->findById($id);

        return new BookResource($book);
    }

    public function update(UpdateBookRequest $request, string $userId, string $id)
    {
        $data = $request->validated();

        $user = $this->userRepository->findById($userId);
        $book = $this->bookRepository->user($user->id())->findById($id);

        $updatedBook = $this->bookRepository->update($book->reference(), $data);

        if ($request->has('number_of_pages')) {
            $this->bookCounterService->update(
                $user->id(),
                $book->data()['number_of_pages'],
                $request->get('number_of_pages'),
            );
        }

        return new BookResource($updatedBook);
    }

    public function destroy(string $userId, string $id)
    {
        $user = $this->userRepository->findById($userId);
        $book = $this->bookRepository->user($user->id())->findById($id);

        $book->reference()->delete();

        $this->bookCounterService->decrement($userId, $book->data()['number_of_pages']);

        return response()->noContent();
    }
}
