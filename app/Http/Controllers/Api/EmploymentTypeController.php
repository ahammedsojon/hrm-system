<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\EmploymentType\StoreEmploymentTypeRequest;
use App\Http\Requests\EmploymentType\UpdateEmploymentTypeRequest;
use App\Http\Resources\EmploymentTypeResource;
use App\Models\EmploymentType;
use App\Services\EmploymentType\EmploymentTypeService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class EmploymentTypeController extends Controller
{
    public function __construct(
        private readonly EmploymentTypeService $employmentTypeService,
    ) {}

    public function index(Request $request)
    {
        $employmentTypes = $this->employmentTypeService->all(
            $request->string('search')->toString() ?: null,
        );

        return EmploymentTypeResource::collection($employmentTypes);
    }

    public function store(StoreEmploymentTypeRequest $request): EmploymentTypeResource
    {
        $employmentType = $this->employmentTypeService->create($request->validated());

        return new EmploymentTypeResource($employmentType->loadCount('employees'));
    }

    public function show(EmploymentType $employment_type): EmploymentTypeResource
    {
        return new EmploymentTypeResource($this->employmentTypeService->find($employment_type->id));
    }

    public function update(UpdateEmploymentTypeRequest $request, EmploymentType $employment_type): EmploymentTypeResource
    {
        $employmentType = $this->employmentTypeService->update($employment_type, $request->validated());

        return new EmploymentTypeResource($employmentType);
    }

    public function destroy(EmploymentType $employment_type): Response
    {
        $this->employmentTypeService->delete($employment_type);

        return response()->noContent();
    }
}
