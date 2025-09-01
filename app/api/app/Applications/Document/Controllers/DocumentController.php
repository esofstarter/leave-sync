<?php

namespace App\Applications\Document\Controllers;

use App\Applications\Document\DTO\DocumentDTO;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Applications\Document\Services\DocumentServiceInterface;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @property DocumentServiceInterface $documentService
 */
class DocumentController extends Controller
{
    public function __construct(
        DocumentServiceInterface $documentService
    ) {
        $this->documentService = $documentService;
    }

    /**
     * Get a JSON with all the users
     *
     * @return JsonResponse
     */
    public function getAll(): JsonResponse
    {
        $leaveTypeDTOs = $this->documentService->getAll();
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
        $leaveTypeDTO = $this->documentService->get($id);
        return response()->json($leaveTypeDTO);
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
            $leaveTypesDTO = $this->documentService->draw($data);

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
