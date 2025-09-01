<?php

namespace App\Applications\LeaveType\Repositories;

use App\Applications\Pagination\StarterPaginator;
use App\Applications\LeaveType\DTO\LeaveTypeDTO;
use App\Applications\LeaveType\Model\LeaveType;

/**
 * Interface LeaveTypeRepositoryInterface
 * @package App\Applications\LeaveType
 */

interface LeaveTypeRepositoryInterface
{
    /**
     * @return array
     */
    public function getAll(): array;

    /**
     * @param integer $id
     * @return LeaveType
     */
    public function get($id);

    /**
     * @param LeaveTypeDTO $leaveTypeDTO
     * @return LeaveType
     */
    public function create(LeaveTypeDTO $leaveTypeDTO): LeaveType;

    /**
     * @param int $userId
     * @param LeaveTypeDTO $userData
     * @return LeaveType
     */
    public function update(int $userId, LeaveTypeDTO $userData): LeaveType;

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
