<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Note;

class NotePadController extends Controller
{
    /**
     * This method return a notepad, the notepad contains note title, note context and many tags.
     * 
     * @Param noteId Table note primary key.
     * @Return A real notepad view.
     */
    public function getDetail(string $noteId)
    {
        $note = Note::findOrFail($noteId);
        $tags = $note->tags;
   
        return view('notepad.detail', [
            'note' => $note,
            'tags' => $tags
        ]);
    }

    /**
     * This method use to update a notepad by note id, the notepad contains note title, note context and many tags.
     * 
     * @Param noteId Table note primary key.
     * @Param request This request carries note data that needs to be updated.
     * @Return Whether the update operation was successful.
     */
    public function update(Request $request, $noteId)
    {
        $note = Note::find($noteId);
        $note->title = $request->title;
        $note->content = $request->content;

        //TODO: update note tag relations
    

        //TODO: return updated view

        return $note->save();
    }
}
