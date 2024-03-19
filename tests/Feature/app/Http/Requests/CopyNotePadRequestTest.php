<?php

namespace Tests\Feature\app\Http\Requests;

use App\Models\Note;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CopyNotePadRequestTest extends TestCase
{
    use RefreshDatabase;

    public function testTitleWithNull()
    {
        $note = Note::factory()->create();
        $tagList = Tag::factory()->count(5)->create();
        $note->tags()->attach($tagList);

        $note->title = 2;
        $note->copy_times = 0;
        $note->origin_mark = true;
        $note->save();

        $requestCopyNote = [
            'title' => null,
            'content' => $note->content,
            'copy_times' => $note->copy_times,
            'origin_mark' => $note->origin_mark,
            'tagIds' => $tagList->pluck('id')->toArray()
        ];
        $this->withoutExceptionHandling();
        $this->expectException(\Exception::class);
        $resp = $this->post(route('notepads.copy', ['id' => $note->id]), $requestCopyNote);

    }

    public function testTitleWithEmptySpace()
    {
        $note = Note::factory()->create();
        $tagList = Tag::factory()->count(5)->create();
        $note->tags()->attach($tagList);

        $note->title = 2;
        $note->copy_times = 0;
        $note->origin_mark = true;
        $note->save();

        $requestCopyNote = [
            'title' => '  ',
            'content' => $note->content,
            'copy_times' => $note->copy_times,
            'origin_mark' => $note->origin_mark,
            'tagIds' => $tagList->pluck('id')->toArray()
        ];
        $this->withoutExceptionHandling();
        $this->expectException(\Exception::class);
        $resp = $this->post(route('notepads.copy', ['id' => $note->id]), $requestCopyNote);

    }

    public function testTitleWithEmptyOutOfLimit()
    {
        $note = Note::factory()->create();
        $tagList = Tag::factory()->count(5)->create();
        $note->tags()->attach($tagList);

        $note->title = 2;
        $note->copy_times = 0;
        $note->origin_mark = true;
        $note->save();

        $requestCopyNote = [
            'title' => '11111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111',
            'content' => $note->content,
            'copy_times' => $note->copy_times,
            'origin_mark' => $note->origin_mark,
            'tagIds' => $tagList->pluck('id')->toArray()
        ];
        $this->withoutExceptionHandling();
        $this->expectException(\Exception::class);
        $resp = $this->post(route('notepads.copy', ['id' => $note->id]), $requestCopyNote);
    }

    public function testContentWithNull()
    {
        $note = Note::factory()->create();
        $tagList = Tag::factory()->count(5)->create();
        $note->tags()->attach($tagList);

        $note->title = 2;
        $note->copy_times = 0;
        $note->origin_mark = true;
        $note->save();

        $requestCopyNote = [
            'title' => $note->title,
            'content' => null,
            'copy_times' => $note->copy_times,
            'origin_mark' => $note->origin_mark,
            'tagIds' => $tagList->pluck('id')->toArray()
        ];
        $this->withoutExceptionHandling();
        $this->expectException(\Exception::class);
        $resp = $this->post(route('notepads.copy', ['id' => $note->id]), $requestCopyNote);
    }

    public function testTagIdListWithNull()
    {
        $note = Note::factory()->create();
        $tagList = Tag::factory()->count(5)->create();
        $note->tags()->attach($tagList);

        $note->title = 2;
        $note->copy_times = 0;
        $note->origin_mark = true;
        $note->save();

        $requestCopyNote = [
            'title' => $note->title,
            'content' => $note->content,
            'copy_times' => $note->copy_times,
            'origin_mark' => $note->origin_mark,
            'tagIds' => null
        ];
        $this->withoutExceptionHandling();
        $this->expectException(\Exception::class);
        $resp = $this->post(route('notepads.copy', ['id' => $note->id]), $requestCopyNote);
    }

    public function testTagIdListWithNotArray()
    {
        $note = Note::factory()->create();
        $tagList = Tag::factory()->count(5)->create();
        $note->tags()->attach($tagList);

        $note->title = 2;
        $note->copy_times = 0;
        $note->origin_mark = true;
        $note->save();

        $requestCopyNote = [
            'title' => $note->title,
            'content' => $note->content,
            'copy_times' => $note->copy_times,
            'origin_mark' => $note->origin_mark,
            'tagIds' => 1
        ];
        $this->withoutExceptionHandling();
        $this->expectException(\Exception::class);
        $resp = $this->post(route('notepads.copy', ['id' => $note->id]), $requestCopyNote);
    }

    public function testTagIdListWithNotExpectElement()
    {
        $note = Note::factory()->create();
        $tagList = Tag::factory()->count(5)->create();
        $note->tags()->attach($tagList);

        $note->title = 2;
        $note->copy_times = 0;
        $note->origin_mark = true;
        $note->save();

        $requestCopyNote = [
            'title' => $note->title,
            'content' => $note->content,
            'copy_times' => $note->copy_times,
            'origin_mark' => $note->origin_mark,
            'tagIds' => array('ture', 'true')
        ];
        $this->withoutExceptionHandling();
        $this->expectException(\Exception::class);
        $resp = $this->post(route('notepads.copy', ['id' => $note->id]), $requestCopyNote);
    }
}
