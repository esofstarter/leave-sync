<?php

namespace App\Applications\Country\Repositories;

use App\Applications\Pagination\StarterPaginator;
use App\Applications\Country\DTO\CountryDTO;
use App\Applications\Country\Model\Country;

/**
 * Interface CountryRepositoryInterface
 * @package App\Applications\Country
 */

interface CountryRepositoryInterface
{
    /**
     * @return array
     */
    public function getAll(): array;

    /**
     * @param integer $id
     * @return Country
     */
    public function get($id);

    /**
     * @param CountryDTO $countryDTO
     * @return Country
     */
    public function create(CountryDTO $countryDTO): Country;

    /**
     * @param int $userId
     * @param CountryDTO $userData
     * @return Country
     */
    public function update(int $userId, CountryDTO $userData): Country;

    /**
     * @param integer $id
     * @return boolean
     */
    public function delete(int $id);

    /**
     * @param array $data
     * @return StarterPaginator
     */
    public function draw(array $data): StarterPaginator;

}
