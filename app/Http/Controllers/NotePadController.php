<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Models\Tag;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

define("LIMIT", 99);

class NotePadController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse list of notePad.
     */
    public function index(): JsonResponse
    {
        $notePads = Note::with('tags')->get();

        return response()->json($notePads);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @Param Request $request
     * @return JsonResponse Json data contains notePad.
     */
    public function store(Request $request): JsonResponse
    {
        // search before save.
        $existNote = Note::where('title', $request->title)->first();

        // create failed target already exist.
        if ($existNote != null) {
            return response()->json();
        }

        $notePad = Note::create($request->all());

        if ($request->tagIdList != null) {
            // get tags for request data
            $tagList = Tag::whereIn('id', $request->tagIdList)->get();

            // if request contains tag primary key, bind note, tag relationship
            if ($tagList->isNotEmpty()) {
                $notePad->tags()->attach($tagList);
            }

            return response()->json($notePad);
        }
        return response()->json();
    }

    /**
     * Display the specified resource.
     *
     * @param string $id table note primary key.
     * @return JsonResponse Json data contains notePad.
     */
    public function show(string $id): JsonResponse
    {
        $notePad = Note::findOrFail($id);
        return response()->json($notePad);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param string $id table note primary key.
     * @return JsonResponse Json data contains notePad.
     */
    public function edit(string $id): JsonResponse
    {
        $notePad = Note::findOrFail($id);
        return response()->json($notePad);
    }

    /**
     * Update the specified resource in storage.
     *
     * @Param Request $request
     * @param string $id table note primary key.
     * @return JsonResponse Json data contains notePad.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        // first update note's title and content.
        $notePad = Note::findOrFail($id);
        $notePad->fill($request->all());

        // delete old relationship before update
        $notePad->tags()->detach();
        $notePad->tags()->attach($request->tagIdList);
        $notePad->save();

        return response()->json($notePad);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param string $id table note primary key.
     * @return JsonResponse
     */
    public function destroy(string $id): JsonResponse
    {
        $notePad = Note::findOrFail($id);
        $notePad->delete();
        return response()->json([]);
    }

    /**
     * Restore the deleted note.
     *
     * @param string $id table note primary key.
     * @return JsonResponse Json data contains notePad.
     */
    public function restore(string $id): JsonResponse
    {
        $notePad = Note::onlyTrashed()->find($id);
        $notePad->restore();
        return response()->json($notePad);
    }

    /**
     * Clone an exist notePad.
     *
     * @Param request $request request contains note data, a list of tags.
     * @param string $id table note primary key.
     * @return JsonResponse Json data contains notePad.
     */
    public function copy(request $request, string $id): JsonResponse
    {
        $metaNote = Note::find($id);

        /**
         * What is copied is a piece of metadata.Create a suffix by the number of times it is copied.
         *
         * DEFINE 'title' : table note's filed `title` metadata.
         *
         * eg:
         * 'title' = "this is a title"
         * 'title'(1) =  "this is a title"(1)
         *
         * ------------------ copy_times = 0 --------------------
         * new title ----> 'title'(copy_times + 1)
         * new title is "this is a title"(1)
         * ------------------------------------------------------
         *
         * ------------------ copy_times = 1 --------------------
         * new title ----> 'title'(copy_times + 1)
         * new title is "this is a title(2)"
         * ------------------------------------------------------
         *
         * ------------------ copy_times = 98 -------------------
         * new title ----> 'title'(copy_times + 1)
         * new title is "this is a title(99)"
         * ------------------------------------------------------
         *
         * ------------------ copy_times = 99 -------------------
         * new title ----> 'title'(copy_times + 1)
         * copy_times + 1 > 99(default limit 99)
         *
         * new title ----> 'title'(99)((copy_times + 1) % 99)
         * new title is "this is a title(99)(1)"
         * ------------------------------------------------------
         *
         * ------------------ copy_times = 100 ------------------
         * new title ----> 'title'(copy_times + 1)
         * copy_times + 1 > 99(default limit 99)
         *
         * new title ----> 'title'(99)((copy_times + 1) % 99)
         * new title is "this is a title(99)(2)"
         * ------------------------------------------------------
         *
         * ------------------ copy_times = 198 ------------------
         * new title ----> 'title'(copy_times + 1)
         * copy_times + 1 > 99(default limit 99)
         *
         * ((copy_times + 1) / 99) = 2(TWO)
         *
         * we should add TWO (99) in new title
         * new title is "this is a title(99)(99)(1)"
         * ------------------------------------------------------
         *
         * ------------------ copy_times = 298 ------------------
         * new title ----> 'title'(copy_times + 1)
         * copy_times + 1 > 99(default limit 99)
         *
         * ((copy_times + 1) / 99) = 3(THREE)
         *
         * we should add THREE (99) in new title
         * new title is "this is a title(99)(99)(99)(2)"
         * -----------------------------------------------------
         */
        if ($metaNote->origin_mark) {
            $clonedNote = new Note();
            $clonedNote->title = $this->incrementTitleSuffix($metaNote);
            $clonedNote->content = $metaNote->content;
            $clonedNote->copy_times = 0;
            $clonedNote->origin_mark = false;
            $clonedNote->save();

            // bind new relationship.
            $tagIdList = $request->tagIdList;
            $clonedNote->tags()->attach($tagIdList);

            return response()->json($clonedNote);
        }

        //TODO what if title like "title(1)" ?
        return response()->json();
    }

    private function incrementTitleSuffix($metaNote): string
    {
        $newTitle = $metaNote->title;
        // the total number of copies
        $totalCopiedTimes = ($metaNote->copy_times + 1);

        // add (LIMIT) suffix
        for ($i = 0; $i < (int)($totalCopiedTimes / LIMIT); $i++) {
            $newTitle = $newTitle . "(" . LIMIT . ")";
        }

        // add calculated suffix
        $calculateNum = $totalCopiedTimes % LIMIT;
        return $newTitle . "(" . $calculateNum . ")";
    }
}
