<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreNoteRequest;
use App\Http\Requests\Api\V1\UpdateNoteRequest;
use App\Http\Resources\Api\V1\NoteResource;
use App\Models\Note;
use Illuminate\Http\Response;
use \Illuminate\Http\JsonResponse;

class NoteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $notes = Note::query()
            ->latest()
            ->get();

        return NoteResource::collection($notes);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreNoteRequest $request): JsonResponse
    {
        $note = Note::create($request->validated());

        return (new NoteResource($note))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Note $note): NoteResource
    {
        return new NoteResource($note);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        UpdateNoteRequest $request,
        Note $note
    ): NoteResource {
        $note->update($request->validated());

        return new NoteResource($note->refresh());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Note $note): Response
    {
        $note->delete();
        return response()->noContent();
    }
}
