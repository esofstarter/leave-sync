<?php

namespace App\Applications\NationalHoliday\Controllers;

use App\Applications\NationalHoliday\DTO\NationalHolidayDTO;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Applications\NationalHoliday\Services\NationalHolidayServiceInterface;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @property NationalHolidayServiceInterface $nationalHolidayService
 */
class NationalHolidayController extends Controller
{
    public function __construct(
        NationalHolidayServiceInterface $nationalHolidayService
    ) {
        $this->nationalHolidayService = $nationalHolidayService;
    }

    /**
     * Get a JSON with all the users
     *
     * @return JsonResponse
     */
    public function getAll(): JsonResponse
    {
        $leaveTypeDTOs = $this->nationalHolidayService->getAll();
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
        $leaveTypeDTO = $this->nationalHolidayService->get($id);
        return response()->json($leaveTypeDTO);
    }

    /**
     * Store user and get JSON with a user response
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function create(Request $request): JsonResponse
    {
        $leaveTypeDTO = NationalHolidayDTO::fromRequestForCreate($request);
        $newLeaveTypeDTO = $this->nationalHolidayService->create($leaveTypeDTO);

        return response()->json($newLeaveTypeDTO);
    }

    /**
     * Update user
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function update(Request $request): JsonResponse
    {
        $nationalHolidayId = Route::current()->parameter('id');
        $dto = NationalHolidayDTO::fromRequest($request);
        $leaveTypeDTO = $this->nationalHolidayService->update(
            $nationalHolidayId,
            $dto
        );
        return response()->json($leaveTypeDTO);
    }

    /**
     * Delete user
     *
     * @return string
     */
    public function delete()
    {
        $nationalHolidayId = Route::current()->parameter('id');
        return $this->nationalHolidayService->delete($nationalHolidayId);
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
            $leaveTypesDTO = $this->nationalHolidayService->draw($data);

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
