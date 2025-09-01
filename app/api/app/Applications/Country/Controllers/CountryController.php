<?php

namespace App\Applications\Country\Controllers;

use App\Applications\Country\DTO\CountryDTO;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Applications\Country\Services\CountryServiceInterface;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Applications\Country\Requests\CountryRequest;
use App\Applications\Country\Requests\NewCountryRequest;

/**
 * @property CountryServiceInterface $countryService
 */
class CountryController extends Controller
{
    public function __construct(
        CountryServiceInterface $countryService
    ) {
        $this->countryService = $countryService;
    }

    /**
     * Get a JSON with all the users
     *
     * @return JsonResponse
     */
    public function getAll(): JsonResponse
    {
        $countryDTOs = $this->countryService->getAll();
        return response()->json($countryDTOs);
    }

    /**
     * Get a JSON with a user by ID
     *
     * @param  integer  $id
     * @return JsonResponse
     */
    public function get(int $id): JsonResponse
    {
        $countryDTO = $this->countryService->get($id);
        return response()->json($countryDTO);
    }

    /**
     * Store user and get JSON with a user response
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function create(NewCountryRequest $request): JsonResponse
    {
        $countryDTO = CountryDTO::fromRequestForCreate($request);
        $newCountryDTO = $this->countryService->create($countryDTO);

        return response()->json($newCountryDTO);
    }

    /**
     * Update user
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function update(CountryRequest $request): JsonResponse
    {
        $countryId = Route::current()->parameter('id');
        $dto = CountryDTO::fromRequest($request);
        $countryDTO = $this->countryService->update(
            $countryId,
            $dto
        );
        return response()->json($countryDTO);
    }

    /**
     * Delete leave Type
     *
     * @return string
     */
    public function delete()
    {
        $countryId = Route::current()->parameter('id');
        return $this->countryService->delete($countryId);
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
            $countriesDTP = $this->countryService->draw($data);

            return response()->json($countriesDTP);
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
