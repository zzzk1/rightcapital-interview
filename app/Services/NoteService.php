<?php

namespace App\Services;

use App\Models\Note;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class NoteService
{
    public function listAll(): Collection
    {
        return Note::all();
    }

    public function storeOne(Request $request)
    {
        return Note::create($request->all());
    }

    public function getOne(string $id): Note
    {
        return Note::findOrFail($id);
    }

    public function updateOne(Request $request, string $id)
    {
        $note = Note::findOrFail($id);
        $note->fill($request->all());

        return $note->save();
    }

    public function removedOne(string $id)
    {
        $note = Note::findOrFail($id);
        return $note->delete();
    }
}
