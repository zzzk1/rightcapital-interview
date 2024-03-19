<?php

namespace App\Http\Controllers;

use App\Services\NoteService;
use App\Services\TagService;
use App\Utils\ApiResult;
use Illuminate\Http\Request;

class TagController extends Controller
{
    protected $tagService;

    public function __construct(TagService $tagService)
    {
        $this->tagService = $tagService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tagList = $this->tagService->listAll();
        return ApiResult::success("list successful", $tagList);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $stored = $this->tagService->storeOne($request);
        return ApiResult::success("stored successful", $stored);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $tag = $this->tagService->getOne($id);
        return ApiResult::success("get successful", $tag);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $updated = $this->tagService->updateOne($request, $id);
        return ApiResult::success("updated successful", $updated);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $removed = $this->tagService->removedOne($id);
        return ApiResult::success("removed successful", $removed);
    }
}
