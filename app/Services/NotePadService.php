<?php
namespace App\Services;

use App\Models\Note;
use App\Models\Tag;
use Illuminate\Http\Request;

define("LIMIT", 99);

class NotePadService
{
    /**
     * Search a listing of notePad.
     *
     * @return array list of all notePad.
     */
    public function listAll(): array
    {
        return Note::with('tags')->get()->toArray();
    }

    /**
     * Get one padNode.
     *
     * @param string $id table notes primary key.
     * @return Note target notePad.
     */
    public function getOne(string $id): Note
    {
        return Note::findOrFail($id);
    }

    /**
     * Get one padNode.
     *
     * @param Request $request
     * @param string $id table notes primary key.
     * @return mixed
     */
    public function updateOne(Request $request, string $id)
    {
        // first update note's title and content.
        $notePad = Note::findOrFail($id);
        $notePad->fill($request->all());

        // delete old relationship before update
        $notePad->tags()->detach();
        $notePad->tags()->attach($request->tagIdList);
        $notePad->save();

        return $notePad;
    }

    /**
     * Make notePad save in database.
     *
     * @param Request $request
     * @return Note target notePad.
     */
    public function saveOne(Request $request):Note
    {
        // search before save.
        $existNote = Note::where('title', $request->title)->first();

        // create failed target already exist.
        if ($existNote != null) {
            return $existNote;
        }

        $notePad = Note::create($request->all());

        if ($request->tagIdList != null) {
            // get tags for request data
            $tagList = Tag::whereIn('id', $request->tagIdList)->get();

            // if request contains tag primary key, bind note, tag relationship
            if ($tagList->isNotEmpty()) {
                $notePad->tags()->attach($tagList);
            }
        }

        return $notePad;
    }

    /**
     * Delete notePad logical.
     *
     * @param string $id table notes primary key.
     * @return mixed
     */
    public function removeOne(string $id)
    {
        $notePad = Note::findOrFail($id);

        return $notePad->delete();
    }

    /**
     * Restore notePad.
     *
     * @param string $id table notes primary key.
     * @return mixed
     */
    public function restoreOne(string $id)
    {
        $notePad = Note::onlyTrashed()->find($id);

        return $notePad->restore();
    }

    /**
     * Clone a notePad by choose notePad.
     *
     * @param Request $request
     * @param string $id table notes primary key.
     * @return Note|null
     */
    public function copyOne(request $request, string $id)
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

            return $clonedNote;
        } else {
            //TODO what if title like "title(1)" ?

            return null;
        }
    }

    /**
     * Generate a new title by copying the notepad title.
     *
     * @param $metaNote data source.
     * @return string new title.
     */
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
