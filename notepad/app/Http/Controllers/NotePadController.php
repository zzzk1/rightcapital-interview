<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Note;
use App\Models\Tag;
use App\Models\NoteTag;

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
     * @Param request This request carries Note and list of Tags belonging to it.
     * @Param noteId Table note primary key.
     * @Return Whether the update operation was successful.
     */
    public function update(Request $request, $noteId)
    {
        // Fisrt update note's title and content.
        $note = Note::findOrFail($noteId);
        $note->title = $request->title;
        $note->content = $request->content;
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
}