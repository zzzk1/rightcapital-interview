<?php

namespace App\Http\Controllers;

use App\Http\Requests\CopyNotePadRequest;
use App\Http\Requests\CreateNotePadRequest;
use App\Http\Requests\CreateTagRequest;
use App\Http\Requests\UpdateNotePadRequest;
use Illuminate\Http\JsonResponse;

use App\Services\NotePadService;
use App\Utils\ApiResult;

class NotePadController extends Controller
{
    protected $notePadService;

    public function __construct(NotePadService $notePadService)
    {
        $this->notePadService = $notePadService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse list of notePad.
     */
    public function index(): JsonResponse
    {
        $notePads = $this->notePadService->listAll();

        return ApiResult::success("list successful", $notePads);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CreateNotePadRequest $request
     * @return JsonResponse Json data contains notePad.
     */
    public function store(CreateNotePadRequest $request): JsonResponse
    {
        $saved = $this->notePadService->saveOne($request);

        return ApiResult::success("list successful", $saved);
    }

    /**
     * Display the specified resource.
     *
     * @param string $id table note primary key.
     * @return JsonResponse Json data contains notePad.
     */
    public function show(string $id): JsonResponse
    {
        $notePad = $this->notePadService->getOne($id);

        return ApiResult::success("get successful", $notePad);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateNotePadRequest $request
     * @param string $id table note primary key.
     * @return JsonResponse Json data contains notePad.
     */
    public function update(UpdateNotePadRequest $request, string $id): JsonResponse
    {
        $updated = $this->notePadService->updateOne($request, $id);

        return ApiResult::success("updated successful", $updated);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param string $id table note primary key.
     * @return JsonResponse
     */
    public function destroy(string $id): JsonResponse
    {
        $removed = $this->notePadService->removeOne($id);
        return ApiResult::success("removed successful", $removed);
    }

    /**
     * Restore the deleted note.
     *
     * @param string $id table note primary key.
     * @return JsonResponse Json data contains notePad.
     */
    public function restore(string $id): JsonResponse
    {
        $restored = $this->notePadService->restoreOne($id);
        return ApiResult::success("restored successful", $restored);
    }

    /**
     * Clone an exist notePad.
     *
     * @param CopyNotePadRequest $request
     * @param string $id table note primary key.
     * @return JsonResponse Json data contains notePad.
     */
    public function copy(CopyNotePadRequest $request, string $id): JsonResponse
    {
        $copied = $this->notePadService->copyOne($request, $id);
        return ApiResult::success("copied successful", $copied);
    }
}
