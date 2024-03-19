<?php

namespace Tests\Feature\app\Http\Requests;

use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateNotePadRequestTest extends TestCase
{
    use RefreshDatabase;

    public function testTitleWithNull()
    {
        $tagList = Tag::factory()->count(5)->create();

        //request contains note, list of tag primary id
        $requestData = [
            'title' => null,
            'content' => 'content',
            'copy_times' => '1',
            'origin_mark' => true,
            'tagIdList' => $tagList->pluck('id')->toArray()
        ];

        $this->withoutExceptionHandling();
        $this->expectException(\Exception::class);
        $response = $this->postJson(route('notepads.store'), $requestData);

    }

    public function testTitleWithEmpty()
    {
        $tagList = Tag::factory()->count(5)->create();

        //request contains note, list of tag primary id
        $requestData = [
            'title' => '   ',
            'content' => 'content',
            'copy_times' => '1',
            'origin_mark' => true,
            'tagIdList' => $tagList->pluck('id')->toArray()
        ];

        $this->withoutExceptionHandling();
        $this->expectException(\Exception::class);
        $response = $this->postJson(route('notepads.store'), $requestData);

    }

    public function testTitleWithOutOfLimit()
    {
        $tagList = Tag::factory()->count(5)->create();

        //request contains note, list of tag primary id
        $requestData = [
            'title' => '111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111',
            'content' => 'content',
            'copy_times' => '1',
            'origin_mark' => true,
            'tagIdList' => $tagList->pluck('id')->toArray()
        ];

        $this->withoutExceptionHandling();
        $this->expectException(\Exception::class);
        $response = $this->postJson(route('notepads.store'), $requestData);

    }

    public function testContentNotString()
    {
        $tagList = Tag::factory()->count(5)->create();

        //request contains note, list of tag primary id
        $requestData = [
            'title' => '1111',
            'content' => true,
            'copy_times' => '1',
            'origin_mark' => true,
            'tagIdList' => $tagList->pluck('id')->toArray()
        ];

        $this->withoutExceptionHandling();
        $this->expectException(\Exception::class);
        $response = $this->postJson(route('notepads.store'), $requestData);
    }
}
