<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
     * @Param Request $request
     * @return JsonResponse Json data contains notePad.
     */
    public function store(Request $request): JsonResponse
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
     * @Param Request $request
     * @param string $id table note primary key.
     * @return JsonResponse Json data contains notePad.
     */
    public function update(Request $request, string $id): JsonResponse
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
     * @Param request $request request contains note data, a list of tags.
     * @param string $id table note primary key.
     * @return JsonResponse Json data contains notePad.
     */
    public function copy(request $request, string $id): JsonResponse
    {
        $copied = $this->notePadService->copyOne($request, $id);
        return ApiResult::success("copied successful", $copied);
    }
}
