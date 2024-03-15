<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Note;

class NotePadController extends Controller
{
    /**
     * This method return a notepad, which contains title, context, tags.
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
}
