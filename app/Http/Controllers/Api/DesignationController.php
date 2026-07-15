<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Designation\StoreDesignationRequest;
use App\Http\Requests\Designation\UpdateDesignationRequest;
use App\Http\Resources\DesignationResource;
use App\Models\Designation;
use App\Services\Designation\DesignationService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DesignationController extends Controller
{
    public function __construct(
        private readonly DesignationService $designationService,
    ) {}

    public function index(Request $request)
    {
        $designations = $this->designationService->all(
            $request->string('search')->toString() ?: null,
        );

        return DesignationResource::collection($designations);
    }

    public function store(StoreDesignationRequest $request): DesignationResource
    {
        $designation = $this->designationService->create($request->validated());

        return new DesignationResource($designation->loadCount('employees'));
    }

    public function show(Designation $designation): DesignationResource
    {
        return new DesignationResource($this->designationService->find($designation->id));
    }

    public function update(UpdateDesignationRequest $request, Designation $designation): DesignationResource
    {
        $designation = $this->designationService->update($designation, $request->validated());

        return new DesignationResource($designation);
    }

    public function destroy(Designation $designation): Response
    {
        $this->designationService->delete($designation);

        return response()->noContent();
    }
}
