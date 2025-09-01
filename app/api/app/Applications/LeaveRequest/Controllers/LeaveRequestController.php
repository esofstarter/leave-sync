<?php

namespace App\Applications\LeaveRequest\Controllers;

use App\Applications\LeaveRequest\DTO\LeaveRequestDTO;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Applications\LeaveRequest\Services\LeaveRequestServiceInterface;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Applications\LeaveRequest\Requests\NewLeaveRequestRequest;
use App\Applications\LeaveRequest\Requests\LeaveRequestRequest;
use Storage;
use App\Applications\LeaveRequest\Model\LeaveRequest;

/**
 * @property LeaveRequestServiceInterface $leaveRequestService
 */
class LeaveRequestController extends Controller
{
    public function __construct(
        LeaveRequestServiceInterface $leaveRequestService
    ) {
        $this->leaveRequestService = $leaveRequestService;
    }

    /**
     * Get a JSON with all the users
     *
     * @return JsonResponse
     */
    public function getAll(): JsonResponse
    {
        $leaveRequestDTOs = $this->leaveRequestService->getAll();
        return response()->json($leaveRequestDTOs);
    }

    /**
     * Get a JSON with all the users
     *
     * @return JsonResponse
     */
    public function getApproved(): JsonResponse
    {
        $leaveRequestDTOs = $this->leaveRequestService->getApproved();
        return response()->json($leaveRequestDTOs);
    }

        /**
     * Get a JSON with all the users
     *
     * @return JsonResponse
     */
    public function getApprovedByUser(): JsonResponse
    {
        $leaveRequestDTOs = $this->leaveRequestService->getApprovedByUser();
        return response()->json($leaveRequestDTOs);
    }

    /**
     * Get a JSON with all the users
     *
     * @return JsonResponse
     */
    public function getPending(): JsonResponse
    {
        $leaveRequestDTOs = $this->leaveRequestService->getPending();
        return response()->json($leaveRequestDTOs);
    }

    /**
     * Get a JSON with a user by ID
     *
     * @param  integer  $id
     * @return JsonResponse
     */
    public function get(int $id): JsonResponse
    {
        $leaveRequestDTO = $this->leaveRequestService->get($id);
        return response()->json($leaveRequestDTO);
    }

    /**
     * Store user and get JSON with a user response
     *
     * @param  NewLeaveRequestRequest  $request
     * @return JsonResponse
     */
    public function create(NewLeaveRequestRequest $request): JsonResponse
    {
        $leaveRequestDTO = LeaveRequestDTO::fromRequestForCreate($request);
        $newLeaveRequestDTO = $this->leaveRequestService->create($leaveRequestDTO);

        return response()->json($newLeaveRequestDTO);
    }

    /**
     * Update user
     *
     * @param  LeaveRequestRequest  $request
     * @return JsonResponse
     */
    public function update(LeaveRequestRequest $request): JsonResponse
    {
        $leaveRequestId = Route::current()->parameter('id');
        $dto = LeaveRequestDTO::fromRequest($request);
        $leaveRequestDTO = $this->leaveRequestService->update(
            $leaveRequestId,
            $dto
        );
        return response()->json($leaveRequestDTO);
    }

    /**
     * Approve request
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function approve(Request $request): JsonResponse
    { 
        $leaveRequestId = Route::current()->parameter('id');
        $statusConfirmed = 2;
        $dto = LeaveRequestDTO::fromRequest($request);
        $leaveRequestDTO = $this->leaveRequestService->approve(
            $leaveRequestId,
            leaveRequestData: $dto,
            isConfirmed: $statusConfirmed
        );
        return response()->json($leaveRequestDTO);
    }

        /**
     * Approve request
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function approveUpdate(Request $request): JsonResponse
    { 
        $leaveRequestId = Route::current()->parameter('id');
        $statusConfirmed = 2;
        $dto = LeaveRequestDTO::fromRequest($request);
        $leaveRequestDTO = $this->leaveRequestService->approveUpdate(
            $leaveRequestId,
            leaveRequestData: $dto,
            isConfirmed: $statusConfirmed
        );
        return response()->json($leaveRequestDTO);
    }

    /**
     * Decline Request
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function decline(Request $request): JsonResponse
    { 
        $leaveRequestId = Route::current()->parameter('id');
        $statusConfirmed = 1;
        $dto = LeaveRequestDTO::fromRequest($request);
        $leaveRequestDTO = $this->leaveRequestService->decline(
            $leaveRequestId,
            leaveRequestData: $dto,
            isConfirmed: $statusConfirmed
        );
        return response()->json($leaveRequestDTO);
    }
    /**
     * Delete user
     *
     * @return string
     */
    public function delete()
    {
        $leaveRequestId = Route::current()->parameter('id');
        return $this->leaveRequestService->delete($leaveRequestId);
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
            $leaveTypesDTO = $this->leaveRequestService->draw($data);

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
    public function downloadLeaveRequestPDF(string $file_name)
    {
        $filePath = storage_path( "app/public/" . $file_name);

        if (!file_exists($filePath)) {
            abort(404, "PDF not found.");
        }

        return response()->download($filePath, $file_name, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $file_name . '"'
        ]);
    }
    
}
