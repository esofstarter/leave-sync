<?php

namespace App\Applications\LeaveRequest\Repositories;

use App\Applications\Pagination\StarterPaginator;
use App\Applications\LeaveRequest\DTO\LeaveRequestDTO;
use App\Applications\LeaveRequest\Model\LeaveRequest;

/**
 * Interface LeaveRequestRepositoryInterface
 * @package App\Applications\LeaveRequest
 */

interface LeaveRequestRepositoryInterface
{
    /**
     * @return array
     */
    public function getAll(): array;

    /**
     * @return array
     */
    public function getApproved(): array;
    
        /**
     * @return array
     */
    public function getPending(): array;

    /**
     * @param integer $id
     * @return LeaveRequest
     */
    public function get($id);

    /**
     * @param LeaveRequestDTO $leaveRequestDTO
     * @return LeaveRequest
     */
    public function create(LeaveRequestDTO $leaveRequestDTO): LeaveRequest;

    /**
     * @param int $userId
     * @param LeaveRequestDTO $userData
     * @return LeaveRequest
     */
    public function update(int $userId, LeaveRequestDTO $userData): LeaveRequest;

    /**
     * @param int $leaveRequestId
     * @param LeaveRequestDTO $leaveRequestData
     * @param int $isConfirmed
     * @param bool $isUpdate
     * @return LeaveRequest
     */
    public function confirm(int $leaveRequestId, LeaveRequestDTO $leaveRequestData, int $isConfirmed, bool $isUpdate): LeaveRequest;

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
