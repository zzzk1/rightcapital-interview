<?php

namespace Tests\Feature;

use App\Models\Note;
use App\Models\Tag;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class NotePadControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test getting all notePads.
     */
    public function testIndex()
    {
        $noteList = Note::factory()->count(5);
        $tagList = Tag::factory()->count(2);

        foreach ($noteList as $note) {
            $note->tags()->attach($tagList);
        }

        $response = $this->get(route('notepad.index'));
        $response->assertStatus(200);

        foreach ($noteList as $note) {
            $response->assertJsonFragment([
                'id' => $note->id,
                'title' => $note->title,
                'content' => $note->content,
                'copy_times' => $note->copy_times,
                'origin_mark' => $note->origin_mark,
                'tags' => $note->tags->toArray()
            ]);
        }
    }

    /**
     * Test storing a new notePad.
     */
    public function testStore()
    {
        $tagList = Tag::factory()->count(5)->create();

        //request contains note, list of tag primary id
        $requestData = [
            'title' => 'title',
            'content' => 'content',
            'copy_times' => '1',
            'origin_mark' => true,
            'tagIdList' => $tagList->pluck('id')->toArray()
        ];

        $response = $this->postJson(route('notepad.store'), $requestData);
        $response->assertStatus(200);

        $this->assertDatabaseHas('notes', [
            'title' => $requestData['title'],
            'content' => $requestData['content'],
            'copy_times' => $requestData['copy_times'],
            'origin_mark' => $requestData['origin_mark'],
        ]);

        // assert relationship
        $note = Note::where('title', $requestData['title'])->first();
        foreach ($requestData['tagIdList'] as $tagId) {
            $this->assertDatabaseHas('notes_tags', [
                'note_id' => $note->id,
                'tag_id' => $tagId
            ]);
        }
    }

    /**
     * Test getting a notePad.
     */
    public function testShow()
    {
        $notePad = Note::factory()->create();

        $response = $this->getJson(route('notepad.show', ['id' => $notePad->id]));
        $response->assertStatus(200);

        $response->assertJson([
            'message' => 'get successful',
            'data' => [
                'id' => $notePad->id,
                'title' => $notePad->title,
                'content' => $notePad->content,
                'created_at' => $notePad->created_at->toISOString(),
                'updated_at' => $notePad->updated_at->toISOString(),
                'deleted_at' => $notePad->deleted_at ? $notePad->deleted_at->toISOString() : null,
                'copy_times' => $notePad->copy_times,
                'origin_mark' => $notePad->origin_mark,
            ],
        ]);
    }

    /**
     * Test getting a notePad.
     */
    public function testEdit()
    {
        $notePad = Note::factory()->create();

        $response = $this->get(route('notepad.edit', ['id' => $notePad->id]));
        $response->assertStatus(200);

        $response->assertJson([
            'message' => 'edit successful',
            'data' => [
                'id' => $notePad->id,
                'title' => $notePad->title,
                'content' => $notePad->content,
                'created_at' => $notePad->created_at->toISOString(),
                'updated_at' => $notePad->updated_at->toISOString(),
                'deleted_at' => $notePad->deleted_at ? $notePad->deleted_at->toISOString() : null,
                'copy_times' => $notePad->copy_times,
                'origin_mark' => $notePad->origin_mark,
            ],
        ]);
    }

    /**
     * Test updating a notePad.
     */
    public function testUpdate()
    {
        $notePad = Note::factory()->create();

        $tagList = Tag::factory()->count(5)->create();
        $tagIdList = $tagList->pluck('id')->toArray();
        $notePad->tags()->attach($tagIdList);

        $updatedData = [
            'title' => 'title',
            'content' => 'content',
            'copy_times' => 1,
            'origin_mark' => true,
            'tagIdList' => $tagIdList
        ];

        $response = $this->putJson(route('notepad.update', ['id' => $notePad->id]), $updatedData);
        $response->assertStatus(200);

        $this->assertDatabaseHas('notes', [
            'id' => $notePad->id,
            'title' => $updatedData['title'],
            'content' => $updatedData['content'],
            'copy_times' => $updatedData['copy_times'],
            'origin_mark' => $updatedData['origin_mark'],
        ]);

        foreach ($updatedData['tagIdList'] as $tagId) {
            $this->assertDatabaseHas('notes_tags', [
                'note_id' => $notePad->id,
                'tag_id' => $tagId
            ]);
        }
    }

    /**
     * Test soft deleting a notePad.
     */
    public function testDestroy()
    {
        $notePad = Note::factory()->create();

        $response = $this->deleteJson(route('notepad.destroy', ['id' => $notePad->id]));
        $response->assertStatus(200);

        $this->assertSoftDeleted('notes', ['id' => $notePad->id]);
    }

    /**
     * Test restore a notePad.
     */
    public function testRestore()
    {
        $notePad = Note::factory()->create();
        $notePad->delete();

        $response = $this->putJson(route('notepad.restore', ['id' => $notePad->id]));
        $response->assertStatus(200);

        $this->assertDatabaseHas('notes', ['id' => $notePad->id]);
    }

    /**
     * Test copy an origin notePad. title doesn't contain (number)
     */
    public function testCopyWithOriginNoteWithIncreasingOrder(): void
    {
        $note = Note::factory()->create();
        $tagList = Tag::factory()->count(5)->create();
        $note->tags()->attach($tagList);

        //----------------------------test----------------------------
        $note->title = "hello";
        $note->copy_times = 0;
        $note->origin_mark = true;
        $note->save();

        $requestCopyNote = [
            'title' => $note->title,
            'content' => $note->content,
            'copy_times' => $note->copy_times,
            'origin_mark' => $note->origin_mark,
            'tagIds' => $tagList->pluck('id')->toArray()
        ];

        //sent request
        $resp = $this->post(route('notepad.copy', ['id' => $note->id]), $requestCopyNote);

        $resp->assertStatus(200);

        $this->assertDatabaseHas('notes', [
            'title' => $note->title . "(1)",
            'content' => $note->content,
        ]);

        //----------------------------test----------------------------
        $note->title = "hello";
        $note->copy_times = 1;
        $note->save();

        $requestCopyNote = [
            'title' => $note->title,
            'content' => $note->content,
            'copy_times' => $note->copy_times,
            'origin_mark' => $note->origin_mark,
            'tagIds' => $tagList->pluck('id')->toArray()
        ];

        //sent request
        $resp = $this->post(route('notepad.copy', ['id' => $note->id]), $requestCopyNote);
        $resp->assertStatus(200);

        $this->assertDatabaseHas('notes', [
            'title' => $note->title . "(2)",
            'content' => $note->content,
        ]);

        //----------------------------test----------------------------
        $note->title = "hello";
        $note->copy_times = 99;
        $note->save();

        $requestCopyNote = [
            'title' => $note->title,
            'content' => $note->content,
            'copy_times' => $note->copy_times,
            'origin_mark' => $note->origin_mark,
            'tagIds' => $tagList->pluck('id')->toArray()
        ];

        //sent request
        $resp = $this->post(route('notepad.copy', ['id' => $note->id]), $requestCopyNote);
        $resp->assertStatus(200);

        $this->assertDatabaseHas('notes', [
            'title' => $note->title . "(3)",
            'content' => $note->content,
        ]);

        //----------------------------test----------------------------
        $note->title = "hello";
        $note->copy_times = 298;
        $note->save();

        $requestCopyNote = [
            'title' => $note->title,
            'content' => $note->content,
            'copy_times' => $note->copy_times,
            'origin_mark' => $note->origin_mark,
            'tagIds' => $tagList->pluck('id')->toArray()
        ];

        //sent request
        $resp = $this->post(route('notepad.copy', ['id' => $note->id]), $requestCopyNote);
        $resp->assertStatus(200);

        $this->assertDatabaseHas('notes', [
            'title' => $note->title . "(4)",
            'content' => $note->content,
        ]);
    }

    public function testCopyWithOutOriginNotePadIncreasingOrder()
    {
        $note = Note::factory()->create();
        $tagList = Tag::factory()->count(5)->create();
        $note->tags()->attach($tagList);

        //----------------------------test----------------------------
        $note->title = "Hello World(1)";
        $note->copy_times = 0;
        $note->origin_mark = true;
        $note->save();

        $requestCopyNote = [
            'title' => $note->title,
            'content' => $note->content,
            'copy_times' => $note->copy_times,
            'origin_mark' => $note->origin_mark,
            'tagIds' => $tagList->pluck('id')->toArray()
        ];

        //sent request
        $resp = $this->post(route('notepad.copy', ['id' => $note->id]), $requestCopyNote);

        $resp->assertStatus(200);

        $this->assertDatabaseHas('notes', [
            'title' => "Hello World(2)",
            'content' => $note->content,
        ]);

        //----------------------------test----------------------------
        $note->title = "Hello World(98)";
        $note->copy_times = 0;
        $note->origin_mark = true;
        $note->save();

        $requestCopyNote = [
            'title' => $note->title,
            'content' => $note->content,
            'copy_times' => $note->copy_times,
            'origin_mark' => $note->origin_mark,
            'tagIds' => $tagList->pluck('id')->toArray()
        ];

        //sent request
        $resp = $this->post(route('notepad.copy', ['id' => $note->id]), $requestCopyNote);

        $resp->assertStatus(200);

        $this->assertDatabaseHas('notes', [
            'title' => "Hello World(99)",
            'content' => $note->content,
        ]);

        //----------------------------test----------------------------
        $note->title = "Hello World(99)";
        $note->copy_times = 0;
        $note->origin_mark = true;
        $note->save();

        $requestCopyNote = [
            'title' => $note->title,
            'content' => $note->content,
            'copy_times' => $note->copy_times,
            'origin_mark' => $note->origin_mark,
            'tagIds' => $tagList->pluck('id')->toArray()
        ];

        //sent request
        $resp = $this->post(route('notepad.copy', ['id' => $note->id]), $requestCopyNote);

        $resp->assertStatus(200);

        $this->assertDatabaseHas('notes', [
            'title' => "Hello World(99)(1)",
            'content' => $note->content,
        ]);

        //----------------------------test----------------------------
        $note->title = "Hello World(99)(2)";
        $note->copy_times = 0;
        $note->origin_mark = true;
        $note->save();

        $requestCopyNote = [
            'title' => $note->title,
            'content' => $note->content,
            'copy_times' => $note->copy_times,
            'origin_mark' => $note->origin_mark,
            'tagIds' => $tagList->pluck('id')->toArray()
        ];

        //sent request
        $resp = $this->post(route('notepad.copy', ['id' => $note->id]), $requestCopyNote);

        $resp->assertStatus(200);

        $this->assertDatabaseHas('notes', [
            'title' => "Hello World(99)(3)",
            'content' => $note->content,
        ]);
    }

    public function testCopyWithOutOriginNotePad1()
    {
        //----------------------------creat test data ----------------------------
        $note1 = Note::factory()->create();
        $tagList1 = Tag::factory()->count(2)->create();
        $note1->tags()->attach($tagList1);
        $note1->title = "Hello World(1)";
        $note1->copy_times = 0;
        $note1->origin_mark = true;
        $note1->save();

        $note2 = Note::factory()->create();
        $tagList2 = Tag::factory()->count(2)->create();
        $note2->tags()->attach($tagList2);
        $note2->title = "Hello World(2)";
        $note2->copy_times = 0;
        $note2->origin_mark = true;
        $note2->save();

        $note3 = Note::factory()->create();
        $tagList3 = Tag::factory()->count(2)->create();
        $note3->tags()->attach($tagList3);
        $note3->title = "Hello World(3)";
        $note3->copy_times = 0;
        $note3->origin_mark = true;
        $note3->save();

        //----------------------------creat copy data ----------------------------
        $requestCopyNote = [
            'title' => $note1->title,
            'content' => $note1->content,
            'copy_times' => $note1->copy_times,
            'origin_mark' => $note1->origin_mark,
            'tagIds' => $tagList1->pluck('id')->toArray()
        ];

        //----------------------------send request -------------------------------
        $resp = $this->post(route('notepad.copy', ['id' => $note1->id]), $requestCopyNote);

        $resp->assertStatus(200);

        $this->assertDatabaseHas('notes', [
            'title' => "Hello World(4)",
            'content' => $note1->content,
        ]);
    }

    public function testCopyWithOutOriginNotePad2()
    {
        //----------------------------creat test data ----------------------------
        $note1 = Note::factory()->create();
        $tagList1 = Tag::factory()->count(2)->create();
        $note1->tags()->attach($tagList1);
        $note1->title = "Hello World(99)";
        $note1->copy_times = 0;
        $note1->origin_mark = true;
        $note1->save();

        $note2 = Note::factory()->create();
        $tagList2 = Tag::factory()->count(2)->create();
        $note2->tags()->attach($tagList2);
        $note2->title = "Hello World(99)(1)";
        $note2->copy_times = 0;
        $note2->origin_mark = true;
        $note2->save();

        $note3 = Note::factory()->create();
        $tagList3 = Tag::factory()->count(2)->create();
        $note3->tags()->attach($tagList3);
        $note3->title = "Hello World(99)(2)";
        $note3->copy_times = 0;
        $note3->origin_mark = true;
        $note3->save();

        //----------------------------creat copy data ----------------------------
        $requestCopyNote = [
            'title' => $note1->title,
            'content' => $note1->content,
            'copy_times' => $note1->copy_times,
            'origin_mark' => $note1->origin_mark,
            'tagIds' => $tagList1->pluck('id')->toArray()
        ];

        //----------------------------send request -------------------------------
        $resp = $this->post(route('notepad.copy', ['id' => $note1->id]), $requestCopyNote);

        $resp->assertStatus(200);

        $this->assertDatabaseHas('notes', [
            'title' => "Hello World(99)(3)",
            'content' => $note1->content,
        ]);
    }

    public function testCopyWithOutOriginNotePad3()
    {
        //----------------------------creat test data ----------------------------
        $note1 = Note::factory()->create();
        $tagList1 = Tag::factory()->count(2)->create();
        $note1->tags()->attach($tagList1);
        $note1->title = "Hello World(99)(99)";
        $note1->copy_times = 0;
        $note1->origin_mark = true;
        $note1->save();

        $note2 = Note::factory()->create();
        $tagList2 = Tag::factory()->count(2)->create();
        $note2->tags()->attach($tagList2);
        $note2->title = "Hello World(99)(99)(1)";
        $note2->copy_times = 0;
        $note2->origin_mark = true;
        $note2->save();

        $note3 = Note::factory()->create();
        $tagList3 = Tag::factory()->count(2)->create();
        $note3->tags()->attach($tagList3);
        $note3->title = "Hello World(99)(99)(2)";
        $note3->copy_times = 0;
        $note3->origin_mark = true;
        $note3->save();

        //----------------------------creat copy data ----------------------------
        $requestCopyNote = [
            'title' => $note1->title,
            'content' => $note1->content,
            'copy_times' => $note1->copy_times,
            'origin_mark' => $note1->origin_mark,
            'tagIds' => $tagList1->pluck('id')->toArray()
        ];

        //----------------------------send request -------------------------------
        $resp = $this->post(route('notepad.copy', ['id' => $note1->id]), $requestCopyNote);

        $resp->assertStatus(200);

        $this->assertDatabaseHas('notes', [
            'title' => "Hello World(99)(99)(3)",
            'content' => $note1->content,
        ]);
    }
}
