<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Employee\StoreEmployeeRequest;
use App\Http\Requests\Employee\UpdateEmployeeRequest;
use App\Http\Resources\EmployeeResource;
use App\Http\Resources\EmployeeSummaryResource;
use App\Models\Employee;
use App\Services\Employee\EmployeeService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class EmployeeController extends Controller
{
    public function __construct(
        private readonly EmployeeService $employeeService,
    ) {}

    public function index(Request $request)
    {
        $employees = $this->employeeService->paginate(
            $request->string('search')->toString() ?: null,
            $request->integer('per_page', 15),
        );

        return EmployeeResource::collection($employees);
    }

    public function managers(Request $request)
    {
        $managers = $this->employeeService->managers(
            $request->integer('exclude') ?: null,
        );

        return EmployeeSummaryResource::collection($managers);
    }

    public function store(StoreEmployeeRequest $request): EmployeeResource
    {
        $employee = $this->employeeService->create(
            $request->safe()->except('profile_photo'),
            $request->file('profile_photo'),
        );

        return new EmployeeResource($employee);
    }

    public function show(Employee $employee): EmployeeResource
    {
        return new EmployeeResource($this->employeeService->find($employee->id));
    }

    public function update(UpdateEmployeeRequest $request, Employee $employee): EmployeeResource
    {
        $employee = $this->employeeService->update(
            $employee,
            $request->safe()->except(['profile_photo', 'remove_profile_photo']),
            $request->file('profile_photo'),
            $request->boolean('remove_profile_photo'),
        );

        return new EmployeeResource($employee);
    }

    public function destroy(Employee $employee): Response
    {
        $this->employeeService->delete($employee);

        return response()->noContent();
    }
}
