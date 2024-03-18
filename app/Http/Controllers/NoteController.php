<?php

namespace App\Http\Controllers;

use App\Services\NoteService;
use App\Utils\ApiResult;
use Illuminate\Http\Request;

class NoteController extends Controller
{
    protected $noteService;

    public function __construct(NoteService $noteService)
    {
        $this->noteService = $noteService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $noteList = $this->noteService->listAll();
        return ApiResult::success("list successful", $noteList);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $stored = $this->noteService->storeOne($request);
        return ApiResult::success("stored successful", $stored);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $note = $this->noteService->getOne($id);
        return ApiResult::success("get successful", $note);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $note = $this->noteService->getOne($id);
        return ApiResult::success("edited successful", $note);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $updated = $this->noteService->updateOne($request, $id);
        return ApiResult::success("updated successful", $updated);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $removed = $this->noteService->removedOne($id);
        return ApiResult::success("removed successful", $removed);
    }
}
