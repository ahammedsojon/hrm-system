<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Department\StoreDepartmentRequest;
use App\Http\Requests\Department\UpdateDepartmentRequest;
use App\Http\Resources\DepartmentResource;
use App\Models\Department;
use App\Services\Department\DepartmentService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DepartmentController extends Controller
{
    public function __construct(
        private readonly DepartmentService $departmentService,
    ) {}

    public function index(Request $request)
    {
        $departments = $this->departmentService->all(
            $request->string('search')->toString() ?: null,
        );

        return DepartmentResource::collection($departments);
    }

    public function store(StoreDepartmentRequest $request): DepartmentResource
    {
        $department = $this->departmentService->create($request->validated());

        return new DepartmentResource($department->loadCount('employees'));
    }

    public function show(Department $department): DepartmentResource
    {
        return new DepartmentResource($this->departmentService->find($department->id));
    }

    public function update(UpdateDepartmentRequest $request, Department $department): DepartmentResource
    {
        $department = $this->departmentService->update($department, $request->validated());

        return new DepartmentResource($department);
    }

    public function destroy(Department $department): Response
    {
        $this->departmentService->delete($department);

        return response()->noContent();
    }
}
