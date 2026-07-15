<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Shift\StoreShiftRequest;
use App\Http\Requests\Shift\UpdateShiftRequest;
use App\Http\Resources\ShiftResource;
use App\Models\Shift;
use App\Services\Shift\ShiftService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ShiftController extends Controller
{
    public function __construct(
        private readonly ShiftService $shiftService,
    ) {}

    public function index(Request $request)
    {
        $shifts = $this->shiftService->all(
            $request->string('search')->toString() ?: null,
        );

        return ShiftResource::collection($shifts);
    }

    public function store(StoreShiftRequest $request): ShiftResource
    {
        $shift = $this->shiftService->create($request->validated());

        return new ShiftResource($shift->loadCount('employees'));
    }

    public function show(Shift $shift): ShiftResource
    {
        return new ShiftResource($this->shiftService->find($shift->id));
    }

    public function update(UpdateShiftRequest $request, Shift $shift): ShiftResource
    {
        $shift = $this->shiftService->update($shift, $request->validated());

        return new ShiftResource($shift);
    }

    public function destroy(Shift $shift): Response
    {
        $this->shiftService->delete($shift);

        return response()->noContent();
    }
}
