<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Note;
use App\Models\Tag;
use App\Models\NoteTag;

use App\Http\Requests\UpdateNotePadRequest;
use App\Http\Requests\CreateNotePadRequest;
use App\Http\Requests\CopyNotePadRequest;

define("LIMIT", 99);
class NotePadController extends Controller
{

    /**
     * This method return a notepad, the notepad contains note title, note context and many tags.
     *
     * @Param noteId Table note primary key.
     * @Return detail data.
     */
    public function getDetail(string $noteId)
    {
        $note = Note::findOrFail($noteId);
        $tagList = $note->tags;

        $data = [
            'note' => $note,
            'tags' => $tagList
        ];

        return response()->json($data);
    }

    /**
     * This method use to update a notepad by note id, the notepad contains note title, note context and many tags.
     * We choice delete old bind relationship first, then bind new relationship.
     *
     * @Param UpdateNotePadRequest This request carries Note and list of Tags belonging to it.
     * @Param noteId Table note primary key.
     * @Return Whether the update operation was successful.
     */
    public function update(UpdateNotePadRequest $request, $noteId)
    {
        // Fisrt update note's title and content.
        $note = Note::findOrFail($noteId);
        $note->title = $request->title;
        $note->content = $request->content;
        $note->copy_times = $request->copy_times;
        $note->origin_mark = $request->origin_mark;
        $note->save();

        // delete all old relationship.
        NoteTag::where('note_id', $noteId)->delete();

        // bind new relationship.
        $tagIdList = $request->tagIds;

        // TODO: This operation may not perform well
        foreach ($tagIdList as $tagId) {
            NoteTag::create([
                'note_id' => $noteId,
                'tag_id' => $tagId
            ]);
        }

        $tagList = $note->tags;

        $data = [
            'note' => $note,
            'tags' => $tagList
        ];

        return response()->json($data);
    }

    /**
     * This method can be used to delete any notepad of our choice.
     *
     * @Param request Http request.
     * @Param noteId Table note primary key.
     */
    public function delete($noteId)
    {
        // First unbind relationship logical.
        NoteTag::where('note_id', $noteId)->delete();

        // Delete note logical.
        $note = Note::find($noteId);
        $note->delete();

        return response()->json([]);
    }

    /**
     * Create a new notepad must contains title, content. tag can be empty.
     *
     * @Param CreateNotePadRequest
     */
    public function create(CreateNotePadRequest $request)
    {
        // search before save.
        $existNote = Note::where('title', $request->title)->first();

        // create failed target already exist.
        if ($existNote != null) {
            return response()->json([]);
        }

        $note = new Note();
        $note->title = $request->title;
        $note->content = $request->content;
        $note->copy_times = $request->copy_times;
        $note->origin_mark = $request->origin_mark;
        $note->save();

        return response()->json([]);
    }

    /**
     * restore a deleted note.
     *
     * @param noteId note primary key.
     * @return Whether the copy operation was successful.
     */
    public function restore($noteId)
    {
        $note = Note::onlyTrashed()->find($noteId);
        $note->restore();
        return response()->json([]);
    }

    /**
     * Copy a exist notepad. if still use default title, introduce a rule change it.
     *
     * @Param request Request must contains two things:
     * 1. Detail of note, such as primary key, title, content.
     * 2. List of tag primary key.
     * @Param noteId note primary key.
     *
     * @Return Whether the copy operation was successful.
     */
    public function copy(request $request, $noteId)
    {
        $metaNote = Note::find($noteId);

        /**
         * What is copied is a piece of metadata.Create a suffix by the number of times it is copied.
         *
         * DEFINE 'title' : table note's filed `title` matedata.
         *
         * eg:
         * 'title' = "this is a title"
         * 'title'(1) =  "this is a title"(1)
         *
         * ----------- copy_times = 0 ----------------------------
         * new title ----> 'title'(copy_times + 1)
         * new title is "this is a title"(1)
         * -------------------------------------------------------
         *
         * ----------- copy_times = 1 ----------------------------
         * new title ----> 'title'(copy_times + 1)
         * new title is "this is a title(2)"
         * -------------------------------------------------------
         *
         * ----------- copy_times = 98 ---------------------------
         * new title ----> 'title'(copy_times + 1)
         * new title is "this is a title(99)"
         * -------------------------------------------------------
         *
         * ----------- copy_times = 99 ---------------------------
         * new title ----> 'title'(copy_times + 1)
         * casue copy_times + 1 > 99(default limit 99)
         * new title ----> 'title'(99)((copy_times + 1) % 99)
         * new title is "this is a title(99)(1)"
         * --------------------------------------------------------
         *
         * ----------- copy_times = 100 ---------------------------
         * new title ----> 'title'(copy_times + 1)
         * casue copy_times + 1 > 99(default limit 99)
         * new title ----> 'title'(99)((copy_times + 1) % 99)
         * new title is "this is a title(99)(2)"
         * --------------------------------------------------------
         *
         * ----------- copy_times = 198 ---------------------------
         * new title ----> 'title'(copy_times + 1)
         * casue copy_times + 1 > 99(default limit 99)
         *
         * ((copy_times + 1) / 99) = 2(TWO)
         * we should add TWO (99) in new title
         * new title is "this is a title(99)(99)(1)"
         * --------------------------------------------------------
         *
         * ----------- copy_times = 298 ---------------------------
         * new title ----> 'title'(copy_times + 1)
         * casue copy_times + 1 > 99(default limit 99)
         *
         * ((copy_times + 1) / 99) = 3(THREE)
         * we should add THREE (99) in new title
         * new title is "this is a title(99)(99)(99)(2)"
         * --------------------------------------------------------
         */
        if ($metaNote->origin_mark) {
            $clonedNote = new Note();
            $clonedNote->title = $this->incrementTitleSuffix($metaNote);
            $clonedNote->content = $metaNote->content;
            $clonedNote->copy_times = 0;
            $clonedNote->origin_mark = false;
            $clonedNote->save();

            // bind new relationship.
            $tagIdList = $request->tagIds;

            // TODO: This operation may not perform well
            foreach ($tagIdList as $tagId) {
                NoteTag::create([
                    'note_id' => $clonedNote->id,
                    'tag_id' => $tagId
                ]);
            }

            $tagList = $clonedNote->tags;

            $data = [
                'note' => $clonedNote,
                'tags' => $tagList
            ];

            return $data;
        }

        //TODO what if title like "title(1)" ?
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
