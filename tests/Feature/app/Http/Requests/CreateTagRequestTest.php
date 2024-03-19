<?php

namespace Tests\Feature\app\Http\Requests;

use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateTagRequestTest extends TestCase
{
    use RefreshDatabase;

    public function testNameWithNull()
    {
        $storeTagRequest = [
            'name' => null,
        ];

        $this->withoutExceptionHandling();
        $this->expectException(\Exception::class);
        $response = $this->post(route('tags.store'), $storeTagRequest);
    }

    public function testNameWithEmpty()
    {
        $storeTagRequest = [
            'name' => '    ',
        ];

        $this->withoutExceptionHandling();
        $this->expectException(\Exception::class);
        $response = $this->post(route('tags.store'), $storeTagRequest);

    }

    public function testNameWithOutOfLimit()
    {
        $storeTagRequest = [
            'name' => '111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111',
        ];

        $this->withoutExceptionHandling();
        $this->expectException(\Exception::class);
        $response = $this->post(route('tags.store'), $storeTagRequest);
    }

    public function testNameNotString()
    {
        $storeTagRequest = [
            'name' => true,
        ];

        $this->withoutExceptionHandling();
        $this->expectException(\Exception::class);
        $response = $this->post(route('tags.store'), $storeTagRequest);

    }
}
