<?php

namespace App\Applications\LeaveRequest\Services;

use App\Applications\LeaveRequest\DTO\LeaveRequestDTO;
use App\Applications\LeaveRequest\Repositories\LeaveRequestRepositoryInterface;

/**
 * @property LeaveRequestRepositoryInterface $leaveRequestRepository
 */
class LeaveRequestService implements LeaveRequestServiceInterface
{
    public function __construct(
        LeaveRequestRepositoryInterface $leaveRequestRepository
    ) {
        $this->leaveRequestRepository = $leaveRequestRepository;
    }

    public function getAll(): array
    {
        return $this->leaveRequestRepository->getAll();
    }

    public function getApproved(): array
    {
        return $this->leaveRequestRepository->getApproved();
    }

    public function getApprovedByUser(): array
    {
        return $this->leaveRequestRepository->getApprovedByUser();
    }

    public function getPending(): array
    {
        return $this->leaveRequestRepository->getPending();
    }

    public function get($id): LeaveRequestDTO
    {
        return LeaveRequestDTO::fromModel(
            $this->leaveRequestRepository->get($id)
        );
    }

    public function create(LeaveRequestDTO $leaveRequestData): LeaveRequestDTO
    {
        $leaveType = $this->leaveRequestRepository->create($leaveRequestData);
        return LeaveRequestDTO::fromModel($leaveType);
    }

    public function update(int $leaveRequestId, LeaveRequestDTO $leaveRequestData): LeaveRequestDTO
    {
        $leaveType = $this->leaveRequestRepository->update($leaveRequestId, $leaveRequestData);
        return LeaveRequestDTO::fromModel($leaveType);
    }

    public function approve(int $leaveRequestId, LeaveRequestDTO $leaveRequestData, int $isConfirmed): LeaveRequestDTO
    {
        $leaveRequest = $this->leaveRequestRepository->confirm($leaveRequestId, $leaveRequestData, $isConfirmed, false);
        return LeaveRequestDTO::fromModel($leaveRequest);
    }

    public function approveUpdate(int $leaveRequestId, LeaveRequestDTO $leaveRequestData, int $isConfirmed): LeaveRequestDTO
    {
        $leaveRequest = $this->leaveRequestRepository->confirm($leaveRequestId, $leaveRequestData, $isConfirmed, true);
        return LeaveRequestDTO::fromModel($leaveRequest);
    }

    public function decline(int $leaveRequestId, LeaveRequestDTO $leaveRequestData, int $isConfirmed): LeaveRequestDTO
    {
        $leaveRequest = $this->leaveRequestRepository->confirm($leaveRequestId, $leaveRequestData, $isConfirmed, false);
        return LeaveRequestDTO::fromModel($leaveRequest);
    }

    public function delete(int $id)
    {
        return $this->leaveRequestRepository->delete($id);
    }

    public function draw(array $data): array
    {
        $data['columns'] = ['users.first_name', 'users.last_name', 'email', 'roles.id', 'users.is_disabled'];
        $data['length'] = $data['length'] ?? 10;
        $data['column'] = $data['column'] ?? 'users.first_name';
        $data['dir'] = $data['dir'] ?? 'asc';
        $data['search'] = $data['search'] ?? '';
        $data['userId'] = $data['userId'] ?? '';
        $data['draw'] = $data['draw'] ?? 1;
        $data['isList'] = $data['isList'] ?? false;

        $leaveRequestsCollection = $this->leaveRequestRepository->draw($data);

        $leaveRequestDTO = $leaveRequestsCollection->getCollection()->map(function ($leaveRequest) {
            return LeaveRequestDTO::fromModel($leaveRequest);
        });

        return [
            'data' => $leaveRequestDTO,
            'pagination' => $leaveRequestsCollection->toArray()['pagination'],
        ];
    }
}
