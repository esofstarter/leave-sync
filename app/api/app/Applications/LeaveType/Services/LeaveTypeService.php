<?php

namespace App\Applications\LeaveType\Services;

use App\Applications\LeaveType\DTO\LeaveTypeDTO;
use App\Applications\LeaveType\Repositories\LeaveTypeRepositoryInterface;

/**
 * @property LeaveTypeRepositoryInterface $leaveTypeRepository
 */
class LeaveTypeService implements LeaveTypeServiceInterface
{
    public function __construct(
        LeaveTypeRepositoryInterface $leaveTypeRepository
    ) {
        $this->leaveTypeRepository = $leaveTypeRepository;
    }

    public function getAll(): array
    {
        return $this->leaveTypeRepository->getAll();
    }

    public function get($id): LeaveTypeDTO
    {
        return LeaveTypeDTO::fromModel(
            $this->leaveTypeRepository->get($id)
        );
    }

    public function create(LeaveTypeDTO $leaveTpyeData): LeaveTypeDTO
    {
        $leaveType = $this->leaveTypeRepository->create($leaveTpyeData);
        return LeaveTypeDTO::fromModel($leaveType);
    }

    public function update(int $leaveTypeId, LeaveTypeDTO $leaveTpyeData): LeaveTypeDTO
    {
        $leaveType = $this->leaveTypeRepository->update($leaveTypeId, $leaveTpyeData);
        return LeaveTypeDTO::fromModel($leaveType);
    }

    public function delete(int $id)
    {
        return $this->leaveTypeRepository->delete($id);
    }

    public function draw(array $data): array
    {
        $data['columns'] = ['users.first_name', 'users.last_name', 'email', 'roles.id', 'users.is_disabled'];
        $data['length'] = $data['length'] ?? 10;
        $data['column'] = $data['column'] ?? 'users.first_name';
        $data['dir'] = $data['dir'] ?? 'asc';
        $data['search'] = $data['search'] ?? '';
        $data['draw'] = $data['draw'] ?? 1;

        $usersCollection = $this->leaveTypeRepository->draw($data);

        $usersDTOs = $usersCollection->getCollection()->map(function ($user) {
            return LeaveTypeDTO::fromModel($user);
        });

        return [
            'data' => $usersDTOs,
            'pagination' => $usersCollection->toArray()['pagination'],
        ];
    }
}
