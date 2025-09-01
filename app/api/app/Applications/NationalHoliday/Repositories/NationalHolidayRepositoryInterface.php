<?php

namespace App\Applications\NationalHoliday\Repositories;

use App\Applications\Pagination\StarterPaginator;
use App\Applications\NationalHoliday\DTO\NationalHolidayDTO;
use App\Applications\NationalHoliday\Model\NationalHoliday;

/**
 * Interface NationalHolidayRepositoryInterface
 * @package App\Applications\NationalHoliday
 */

interface NationalHolidayRepositoryInterface
{
    /**
     * @return array
     */
    public function getAll(): array;

    /**
     * @param integer $id
     * @return NationalHoliday
     */
    public function get($id);

    /**
     * @param NationalHolidayDTO $NationalHolidayDTO
     * @return NationalHoliday
     */
    public function create(NationalHolidayDTO $NationalHolidayDTO): NationalHoliday;

    /**
     * @param int $userId
     * @param NationalHolidayDTO $userData
     * @return NationalHoliday
     */
    public function update(int $userId, NationalHolidayDTO $userData): NationalHoliday;

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
