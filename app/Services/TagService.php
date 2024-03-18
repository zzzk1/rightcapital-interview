<?php

namespace App\Services;

use App\Models\Tag;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class TagService
{
    public function listAll(): Collection
    {
        return Tag::all();
    }

    public function storeOne(Request $request)
    {
        return Tag::create($request->all());
    }

    public function getOne(string $id): Tag
    {
        return Tag::findOrFail($id);
    }

    public function updateOne(Request $request, string $id)
    {
        $tag = Tag::findOrFail($id);
        $tag->fill($request->all());

        return $tag->save();
    }

    public function removedOne(string $id)
    {
        $tag = Tag::findOrFail($id);
        return $tag->delete();
    }
}
