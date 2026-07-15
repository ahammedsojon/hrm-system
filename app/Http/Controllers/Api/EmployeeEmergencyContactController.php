<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\EmployeeEmergencyContact\StoreEmployeeEmergencyContactRequest;
use App\Http\Requests\EmployeeEmergencyContact\UpdateEmployeeEmergencyContactRequest;
use App\Http\Resources\EmployeeEmergencyContactResource;
use App\Models\EmployeeEmergencyContact;
use App\Services\EmployeeEmergencyContact\EmployeeEmergencyContactService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class EmployeeEmergencyContactController extends Controller
{
    public function __construct(
        private readonly EmployeeEmergencyContactService $emergencyContactService,
    ) {}

    public function index(Request $request)
    {
        $contacts = $this->emergencyContactService->paginate(
            $request->string('search')->toString() ?: null,
            $request->integer('per_page', 15),
        );

        return EmployeeEmergencyContactResource::collection($contacts);
    }

    public function store(StoreEmployeeEmergencyContactRequest $request): EmployeeEmergencyContactResource
    {
        $contact = $this->emergencyContactService->create($request->validated());

        return new EmployeeEmergencyContactResource($contact);
    }

    public function show(EmployeeEmergencyContact $emergency_contact): EmployeeEmergencyContactResource
    {
        return new EmployeeEmergencyContactResource($this->emergencyContactService->find($emergency_contact->id));
    }

    public function update(UpdateEmployeeEmergencyContactRequest $request, EmployeeEmergencyContact $emergency_contact): EmployeeEmergencyContactResource
    {
        $contact = $this->emergencyContactService->update($emergency_contact, $request->validated());

        return new EmployeeEmergencyContactResource($contact);
    }

    public function destroy(EmployeeEmergencyContact $emergency_contact): Response
    {
        $this->emergencyContactService->delete($emergency_contact);

        return response()->noContent();
    }
}
