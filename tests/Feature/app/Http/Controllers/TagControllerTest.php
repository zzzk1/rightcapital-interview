<?php
namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;

use Tests\TestCase;
use App\Models\Tag;

class TagControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test getting all tags.
     */
    public function testIndex()
    {
        $tagList = Tag::factory()->count(5)->create();

        $response = $this->get(route('tags.index'));
        $response->assertStatus(200);

        // Check if the data exists in the response
        foreach ($tagList as $tag) {
            $response->assertJsonFragment([
                'id' => $tag->id,
                'name' => $tag->name,
                'created_at' => $tag->created_at,
                'updated_at' => $tag->updated_at,
                'deleted_at' => $tag->deleted_at
            ]);
        }
    }

    /**
     * Test storing a new tag.
     */
    public function testStore()
    {
        $storeTagRequest = [
            'name' => 'Store',
        ];

        $response = $this->post(route('tags.store'), $storeTagRequest);
        $response->assertStatus(200);

        // Check if exists in the database
        $this->assertDatabaseHas('tags', $storeTagRequest);
    }

    /**
     * Test getting a tag.
     */
    public function testShow()
    {
        $tag = Tag::factory()->create();

        $response = $this->get(route('tags.show', ['id' => $tag->id]));
        $response->assertStatus(200);

        // Check if the data exists in the response
        $response->assertJsonFragment([
            'message' => 'get successful',
            'data' => [
                'id' => $tag->id,
                'name' => $tag->name,
                'created_at' => $tag->created_at,
                'updated_at' => $tag->updated_at,
                'deleted_at' => $tag->deleted_at
            ]
        ]);
    }

    /**
     * Test updating a tag.
     */
    public function testUpdate()
    {
        $tag = Tag::factory()->create();

        $updateData = [
            'name' => 'updated',
        ];

        $response = $this->put(route('tags.update', ['id' => $tag->id]), $updateData);
        $response->assertStatus(200);

        // Check if exists in the database
        $this->assertDatabaseHas('tags', [
            'id' => $tag->id,
            'name' => $updateData['name'],
        ]);
    }

    /**
     * Test soft deleting a tag.
     */
    public function testDestroy()
    {
        $tag = Tag::factory()->create();

        $response = $this->delete(route('tags.destroy', ['id' => $tag->id]));
        $response->assertStatus(200);

        // Check if the data has been removed
        $this->assertSoftDeleted('tags', [
            'id' => $tag->id,
        ]);
    }
}
