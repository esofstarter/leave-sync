<?php

namespace App\Applications\Country\Services;

use App\Applications\Country\DTO\CountryDTO;
use Illuminate\Http\Request;

/**
 * Interface CountryServiceInterface
 * @package App\Applications\Country
 */

interface CountryServiceInterface
{
    /**
     * @return array
     */
    public function getAll(): array;

    /**
     * @param integer $id
     * @return CountryDTO
     */
    public function get(int $id): CountryDTO;

    /**
     * @param CountryDTO $countryData
     * @return CountryDTO
     */
    public function create(CountryDTO $countryData): CountryDTO;

    /**
     * @param int $countryId
     * @param CountryDTO $countryData
     * @return CountryDTO
     */
    public function update(int $countryId, CountryDTO $countryData): CountryDTO;

    /**
     * @param integer $id
     * @return boolean
     */
    public function delete(int $id);

    /**
     * @param array $data
     * @return array
     */
    public function draw(array $data): array;
}
