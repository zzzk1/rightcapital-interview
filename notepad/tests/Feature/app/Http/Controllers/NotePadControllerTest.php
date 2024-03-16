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
        $UpdatedData = [
            'title' => 'update title',
            'content' => 'update content',
            'tagIds' => $tagList->pluck('id')->toArray()
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
}
