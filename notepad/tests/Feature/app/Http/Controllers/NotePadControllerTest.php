<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;

use Tests\TestCase;

use App\Models\Note;
use App\Models\Tag;

class NotePadControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * This method test  notepad getDetail function.
     */
    public function testgetDetail(): void
    {
        // create fake data.
        $note = Note::factory()->create();
        $tagList = Tag::factory()->count(5)->create();
        $note->tags()->attach($tagList);

        // sent request.
        $resp = $this->get('/notepad/' . $note->id);
        $resp->assertStatus(200);
    }

    /**
     * This method test update function.
     */
    public function testUpdate(): void
    {
        // create fake data.
        $note = Note::factory()->create();
        $tagList = Tag::factory()->count(5)->create();
        $note->tags()->attach($tagList);

        $updatedTagList = Tag::factory()->count(5)->create();
        $UpdatedData = [
            'title' => 'update title',
            'content' => 'update content',
            'copy_times' => 0,
            'origin_mark' => true,
            'tagIds' => $updatedTagList->pluck('id')->toArray()
        ];

        // sent request.
        $resp = $this->post('/notepad/' . $note->id, $UpdatedData);
        $resp->assertStatus(200);

        $this->assertDatabaseHas('notes', [
            'id' => $note->id,
            'title' => 'update title',
            'content' => 'update content'
        ]);

        foreach ($tagList as $tag) {
            $this->assertDatabaseHas('notes_tags', [
                'note_id' => $note->id,
                'tag_id' => $tag->id
            ]);
        }
    }

    /**
     * This method test update function.
     */
    public function testDelete(): void
    {
        // create fake data.
        $note = Note::factory()->create();
        $tagList = Tag::factory()->count(5)->create();
        $note->tags()->attach($tagList);

        // sent request
        $resp = $this->delete('/notepad/' . $note->id);
        $resp->assertStatus(200);

        $this->assertDatabaseHas('notes', [
            'id' => $note->id,
            'title' => $note->title,
            'content' => $note->content
        ]);

        foreach ($tagList as $tag) {
            $this->assertDatabaseHas('notes_tags', [
                'note_id' => $note->id,
                'tag_id' => $tag->id
            ]);
        }
    }

    /**
     * This method test create function.
     */
    public function testCreate(): void
    {
        // create fake data without tag.
        $requestCreateNote = [
            'title' => 'create title',
            'content' => 'create content',
            'copy_times' => 0,
            'origin_mark' => true
        ];
        // sent request
        $resp = $this->post('/notepad/', $requestCreateNote);
        $resp->assertStatus(200);

        $this->assertDatabaseHas('notes', [
            'title' => $requestCreateNote['title'],
            'content' => $requestCreateNote['content']
        ]);
    }

    public function testCopyWithOriginNotePad(): void
    {
        $note = Note::factory()->create();
        $tagList = Tag::factory()->count(5)->create();
        $note->tags()->attach($tagList);

        $note->title = "hello";
        $note->copy_times = 0;
        $note->origin_mark = true;
        $note->save();

        $requestCopyNote = [
            'title' => $note->title,
            'contnet' => $note->content,
            'copy_times' => $note->copy_times,
            'origin_mark' => $note->origin_mark,
            'tagIds'=> $tagList->pluck('id')->toArray()
        ];

        //sent request
        $resp = $this->post('/copy/' . $note->id, $requestCopyNote);
        $resp->assertStatus(200);

        $this->assertDatabaseHas('notes', [
            'title' => $note->title . "(1)",
            'content' => $note->content,
        ]);

        /**************************************************************************************/
        $note->title = "hello";
        $note->copy_times = 1;
        $note->save();

        $requestCopyNote = [
            'title' => $note->title,
            'contnet' => $note->content,
            'copy_times' => $note->copy_times,
            'origin_mark' => $note->origin_mark,
            'tagIds'=> $tagList->pluck('id')->toArray()
        ];

        //sent request
        $resp = $this->post('/copy/' . $note->id, $requestCopyNote);
        $resp->assertStatus(200);

        $this->assertDatabaseHas('notes', [
            'title' => $note->title . "(2)",
            'content' => $note->content,
        ]);

        /**************************************************************************************/
        $note->title = "hello";
        $note->copy_times = 99;
        $note->save();

        $requestCopyNote = [
            'title' => $note->title,
            'contnet' => $note->content,
            'copy_times' => $note->copy_times,
            'origin_mark' => $note->origin_mark,
            'tagIds'=> $tagList->pluck('id')->toArray()
        ];

        //sent request
        $resp = $this->post('/copy/' . $note->id, $requestCopyNote);
        $resp->assertStatus(200);

        $this->assertDatabaseHas('notes', [
            'title' => $note->title . "(99)(1)",
            'content' => $note->content,
        ]);

        /**************************************************************************************/
        $note->title = "hello";
        $note->copy_times = 298;
        $note->save();

        $requestCopyNote = [
            'title' => $note->title,
            'contnet' => $note->content,
            'copy_times' => $note->copy_times,
            'origin_mark' => $note->origin_mark,
            'tagIds'=> $tagList->pluck('id')->toArray()
        ];

        //sent request
        $resp = $this->post('/copy/' . $note->id, $requestCopyNote);
        $resp->assertStatus(200);

        $this->assertDatabaseHas('notes', [
            'title' => $note->title . "(99)(99)(99)(2)",
            'content' => $note->content,
        ]);
    }
}
