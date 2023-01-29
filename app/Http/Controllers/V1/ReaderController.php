<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReaderRequest;
use App\Http\Requests\UpdateReaderRequest;
use App\Http\Resources\ReaderResource;
use App\Repositories\Firestore\ReaderRepository;
use Illuminate\Http\Request;

class ReaderController extends Controller
{
    public function __construct(protected ReaderRepository $readerRepository)
    {
    }

    public function index()
    {
        return ReaderResource::collection($this->readerRepository->all());
    }

    public function store(StoreReaderRequest $request)
    {
        $data = $request->validated();

        $reader = $this->readerRepository->create($data);

        return response()->json(new ReaderResource($reader), 201);
    }

    public function show($id)
    {
        $reader = $this->readerRepository->findById($id);

        return new ReaderResource($reader);
    }

    public function update(UpdateReaderRequest $request, $id)
    {
        $data = $request->validated();

        $reader = $this->readerRepository->findById($id);

        $updatedReader = $this->readerRepository->update($reader->reference(), $data);

        return new ReaderResource($updatedReader);
    }

    public function destroy($id)
    {
        $reader = $this->readerRepository->findById($id);

        $reader->reference()->delete();

        return response()->noContent();
    }
}
