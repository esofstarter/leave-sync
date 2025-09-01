<?php

namespace App\Applications\LeaveType\Controllers;

use App\Applications\LeaveType\DTO\LeaveTypeDTO;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Applications\LeaveType\Services\LeaveTypeServiceInterface;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Applications\LeaveType\Requests\LeaveTypeRequest;
use App\Applications\LeaveType\Requests\NewLeaveTypeRequest;

/**
 * @property LeaveTypeServiceInterface $leaveTypeService
 */
class LeaveTypeController extends Controller
{
    public function __construct(
        LeaveTypeServiceInterface $leaveTypeService
    ) {
        $this->leaveTypeService = $leaveTypeService;
    }

    /**
     * Get a JSON with all the users
     *
     * @return JsonResponse
     */
    public function getAll(): JsonResponse
    {
        $leaveTypeDTOs = $this->leaveTypeService->getAll();
        return response()->json($leaveTypeDTOs);
    }

    /**
     * Get a JSON with a user by ID
     *
     * @param  integer  $id
     * @return JsonResponse
     */
    public function get(int $id): JsonResponse
    {
        $leaveTypeDTO = $this->leaveTypeService->get($id);
        return response()->json($leaveTypeDTO);
    }

    /**
     * Store user and get JSON with a user response
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function create(NewLeaveTypeRequest $request): JsonResponse
    {
        $leaveTypeDTO = LeaveTypeDTO::fromRequestForCreate($request);
        $newLeaveTypeDTO = $this->leaveTypeService->create($leaveTypeDTO);

        return response()->json($newLeaveTypeDTO);
    }

    /**
     * Update user
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function update(LeaveTypeRequest $request): JsonResponse
    {
        $leaveTypeId = Route::current()->parameter('id');
        $dto = LeaveTypeDTO::fromRequest($request);
        $leaveTypeDTO = $this->leaveTypeService->update(
            $leaveTypeId,
            $dto
        );
        return response()->json($leaveTypeDTO);
    }

    /**
     * Delete leave Type
     *
     * @return string
     */
    public function delete()
    {
        $leaveTypeId = Route::current()->parameter('id');
        return $this->leaveTypeService->delete($leaveTypeId);
    }

    /**
     * Get a paginated, filtered and sorted array of Users.
     * This endpoint requires some data in the request.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function draw(Request $request): JsonResponse
    {
        try {
            $data = $request->all();
            $leaveTypesDTO = $this->leaveTypeService->draw($data);

            return response()->json($leaveTypesDTO);
        } catch (\InvalidArgumentException $e) {
            // Handle specific exceptions like InvalidArgumentException
            return response()->json([
                'error' => 'Invalid Argument',
                'message' => $e->getMessage(),
            ], 400); // Bad Request status code
        } catch (\ValidationException $e) {
            // Handle validation exceptions
            return response()->json([
                'error' => 'Validation Error',
                'message' => $e->getMessage(),
                'errors' => $e->errors(),
            ], 422); // Unprocessable Entity status code
        } catch (\Exception $e) {
            // Handle any other general exceptions
            return response()->json([
                'error' => 'Server Error',
                'message' => $e->getMessage(),
            ], 500); // Internal Server Error status code
        }
    }
}
