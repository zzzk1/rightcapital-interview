<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Note;

class NoteControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test getting all notes.
     */
    public function testIndex()
    {
        $notes = Note::factory()->count(5)->create();
        $response = $this->get(route('notes.index'));
        $response->assertStatus(200);

        foreach ($notes as $note) {
            $response->assertJsonFragment([
                'id' => $note->id,
                'title' => $note->title,
                'content' => $note->content,
                'copy_times' => $note->copy_times,
                'origin_mark' => $note->origin_mark,
            ]);
        }
    }

    /**
     * Test storing a new note.
     */
    public function testStore()
    {
        $storeData = [
            'title' => 'Test Note',
            'content' => 'This is a test note content.',
            'copy_times' => 0,
            'origin_mark' => true,
        ];

        $response = $this->post(route('notes.store'), $storeData);
        $response->assertRedirect(route('notes.index'));

        $this->assertDatabaseHas('notes', $storeData);
    }

    /**
     * Test getting a note.
     */
    public function testShow()
    {
        $note = Note::factory()->create();

        $response = $this->get(route('notes.show', ['id' => $note->id]));
        $response->assertStatus(200);

        $response->assertJson([
            'id' => $note->id,
            'title' => $note->title,
            'content' => $note->content,
            'copy_times' => $note->copy_times,
            'origin_mark' => $note->origin_mark,
        ]);
    }

    /**
     * Test updating a note.
     */
    public function testUpdate()
    {
        $note = Note::factory()->create();

        $updateData = [
            'title' => 'Updated Note',
            'content' => 'This is the updated content.',
            'copy_times' => 1,
            'origin_mark' => false,
        ];

        $response = $this->put(route('notes.update', ['id' => $note->id]), $updateData);
        $response->assertStatus(200);

        $this->assertDatabaseHas('notes', [
            'id' => $note->id,
            'title' => $updateData['title'],
            'content' => $updateData['content'],
            'copy_times' => $updateData['copy_times'],
            'origin_mark' => $updateData['origin_mark'],
        ]);
    }

    /**
     * Test soft deleting a note.
     */
    public function testDestroy()
    {
        $note = Note::factory()->create();

        $response = $this->delete(route('notes.destroy', ['id' => $note->id]));
        $response->assertStatus(200);

        $this->assertSoftDeleted('notes', ['id' => $note->id]);
    }
}
