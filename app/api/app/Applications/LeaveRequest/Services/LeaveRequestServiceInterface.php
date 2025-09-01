<?php

namespace App\Applications\LeaveRequest\Services;

use App\Applications\LeaveRequest\DTO\LeaveRequestDTO;
use Illuminate\Http\Request;

/**
 * Interface LeaveRequestServiceInterface
 * @package App\Applications\LeaveRequest
 */

interface LeaveRequestServiceInterface
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
     * @return LeaveRequestDTO
     */
    public function get(int $id): LeaveRequestDTO;

    /**
     * @param LeaveRequestDTO $leaveRequestData
     * @return LeaveRequestDTO
     */
    public function create(LeaveRequestDTO $leaveRequestData): LeaveRequestDTO;

    /**
     * @param int $leaveTypeId
     * @param LeaveRequestDTO $leaveRequestData
     * @return LeaveRequestDTO
     */
    public function update(int $leaveTypeId, LeaveRequestDTO $leaveRequestData): LeaveRequestDTO;

    /**
     * @param int $leaveRequestId
     * @param LeaveRequestDTO $leaveRequestData
     * @param int $isConfirmed
     * @return LeaveRequestDTO
     */
    public function approve(int $leaveRequestId, LeaveRequestDTO $leaveRequestData, int $isConfirmed): LeaveRequestDTO;

    /**
     * @param int $leaveRequestId
     * @param LeaveRequestDTO $leaveRequestData
     * @param int $isConfirmed
     * @return LeaveRequestDTO
     */
    public function approveUpdate(int $leaveRequestId, LeaveRequestDTO $leaveRequestData, int $isConfirmed): LeaveRequestDTO;

        /**
     * @param int $leaveRequestId
     * @param LeaveRequestDTO $leaveRequestData
     * @param int $isConfirmed
     * @return LeaveRequestDTO
     */
    public function decline(int $leaveRequestId, LeaveRequestDTO $leaveRequestData, int $isConfirmed): LeaveRequestDTO;

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
