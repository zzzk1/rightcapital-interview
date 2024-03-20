<?php

namespace Tests\Unit\app\Services;

use App\Models\Note;
use App\Models\Tag;
use App\Services\NotePadService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class NotePadServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $notePadService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->notePadService = new NotePadService();
    }

    public function testCopyOneWithSimpleTitle1()
    {
        $note = Note::factory()->create();
        $tagList = Tag::factory()->count(5)->create();
        $note->tags()->attach($tagList);

        $request = new Request();
        $request->merge([
            'title' => $note->title,
        ]);

        $copyOne = $this->notePadService->copyOne($request, $note->id);

        $this->assertDatabaseHas('notes', [
            'title' => $note->title . "(1)",
        ]);

        self::assertEquals($copyOne->title, $note->title . "(1)");
    }

    public function testCopyOneWithSimpleTitle2()
    {
        $counter = 0;
        Note::factory()->count(2)->create()->each(function ($note) use (&$counter) {
            $tagList = Tag::factory()->count(5)->create();
            $note->tags()->attach($tagList);
            if ($counter == 0) {
                $note->title = 'title';
            } else {
                $note->title = 'title(' . $counter . ')';
            }
            $counter++;
            $note->save();
        });

        $request = new Request();
        $request->merge([
            'title' => 'title',
        ]);

        $copyOne = $this->notePadService->copyOne($request, 1);

        $this->assertDatabaseHas('notes', [
            'title' => "title(2)",
        ]);
        self::assertEquals("title(2)", $copyOne->title);
    }

    public function testCopyOneWithSimpleTitle3()
    {
        $counter = 0;
        Note::factory()->count(99)->create()->each(function ($note) use (&$counter) {
            $tagList = Tag::factory()->count(5)->create();
            $note->tags()->attach($tagList);
            if ($counter == 0) {
                $note->title = 'title';
            } else {
                $note->title = 'title(' . $counter . ')';
            }
            $counter++;
            $note->save();
        });

        $request = new Request();
        $request->merge([
            'title' => 'title',
        ]);

        $copyOne = $this->notePadService->copyOne($request, 1);

        $this->assertDatabaseHas('notes', [
            'title' => "title(99)",
        ]);

        self::assertEquals("title(99)", $copyOne->title);
    }

    public function testCopyOneWithSimpleTitle4()
    {
        $counter = 0;
        Note::factory()->count(100)->create()->each(function ($note) use (&$counter) {
            $tagList = Tag::factory()->count(5)->create();
            $note->tags()->attach($tagList);
            if ($counter == 0) {
                $note->title = 'title';
            } else {
                $note->title = 'title(' . $counter . ')';
            }
            $counter++;
            $note->save();
        });

        $request = new Request();
        $request->merge([
            'title' => 'title',
        ]);

        $copyOne = $this->notePadService->copyOne($request, 1);

        $this->assertDatabaseHas('notes', [
            'title' => $copyOne->title,
        ]);

        self::assertEquals("title(99)(1)", $copyOne->title);
    }

    public function testCopyOneWithComplexTitle1()
    {
        $note = Note::factory()->create();
        $tagList = Tag::factory()->count(5)->create();
        $note->tags()->attach($tagList);
        $note->title = "title(1)";
        $note->save();

        $request = new Request();
        $request->merge([
            'title' => "title(1)",
        ]);

        $copyOne = $this->notePadService->copyOne($request, $note->id);

        $this->assertDatabaseHas('notes', [
            'title' => "title(2)",
        ]);

        self::assertEquals($copyOne->title, "title(2)");
    }

    public function testCopyOneWithComplexTitle2()
    {
        $note = Note::factory()->create();
        $tagList = Tag::factory()->count(5)->create();
        $note->tags()->attach($tagList);
        $note->title = "title(99)";
        $note->save();

        $request = new Request();
        $request->merge([
            'title' => "title(99)",
        ]);

        $copyOne = $this->notePadService->copyOne($request, $note->id);

        $this->assertDatabaseHas('notes', [
            'title' => "title(99)(1)",
        ]);

        self::assertEquals($copyOne->title, "title(99)(1)");
    }

    public function testCopyOneWithComplexTitle3()
    {
        $note = Note::factory()->create();
        $tagList = Tag::factory()->count(5)->create();
        $note->tags()->attach($tagList);
        $note->title = "title(99)(99)";
        $note->save();

        $request = new Request();
        $request->merge([
            'title' => "title(99)(99)",
        ]);

        $copyOne = $this->notePadService->copyOne($request, $note->id);

        $this->assertDatabaseHas('notes', [
            'title' => "title(99)(99)(1)",
        ]);

        self::assertEquals($copyOne->title, "title(99)(99)(1)");
    }

    public function testCopyOneWithComplexTitle4()
    {
        $counter = 0;
        Note::factory()->count(100)->create()->each(function ($note) use (&$counter) {
            $tagList = Tag::factory()->count(5)->create();
            $note->tags()->attach($tagList);
            if ($counter == 0) {
                $note->title = 'title(99)';
            } else {
                $note->title = 'title(99)(' . $counter . ')';
            }
            $counter++;
            $note->save();
        });

        $request = new Request();
        $request->merge([
            'title' => "title(99)(1)",
        ]);

        $copyOne = $this->notePadService->copyOne($request, 2);

        $this->assertDatabaseHas('notes', [
            'title' => "title(99)(99)(1)",
        ]);

        self::assertEquals($copyOne->title, "title(99)(99)(1)");
    }
}
