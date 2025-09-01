<?php

namespace App\Applications\LeaveType\Services;

use App\Applications\LeaveType\DTO\LeaveTypeDTO;
use Illuminate\Http\Request;

/**
 * Interface LeaveTypeServiceInterface
 * @package App\Applications\LeaveType
 */

interface LeaveTypeServiceInterface
{
    /**
     * @return array
     */
    public function getAll(): array;

    /**
     * @param integer $id
     * @return LeaveTypeDTO
     */
    public function get(int $id): LeaveTypeDTO;

    /**
     * @param LeaveTypeDTO $leaveTypeData
     * @return LeaveTypeDTO
     */
    public function create(LeaveTypeDTO $leaveTypeData): LeaveTypeDTO;

    /**
     * @param int $leaveTypeId
     * @param LeaveTypeDTO $leaveTypeData
     * @return LeaveTypeDTO
     */
    public function update(int $leaveTypeId, LeaveTypeDTO $leaveTypeData): LeaveTypeDTO;

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
