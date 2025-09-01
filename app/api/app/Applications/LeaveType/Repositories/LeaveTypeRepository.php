<?php

namespace App\Applications\LeaveType\Repositories;

use App\Applications\LeaveType\DTO\LeaveTypeDTO;
use App\Applications\Pagination\StarterPaginator;
use App\Applications\LeaveType\Model\LeaveType;


/**
 * @property LeaveType $leaveType
 */
class LeaveTypeRepository implements LeaveTypeRepositoryInterface
{
    public function __construct(
        LeaveType $leaveType,
    ) {
        $this->leaveType = $leaveType;
    }

    private const COLUMNS_MAP = [
        'slug' => 'leave_types.slug',
        'name' => 'leave_types.name',
        'color' => 'leave_types.color',
        'is_paid' => 'leave_types.is_paid'
    ];

    public function getAll(): array
    {
        $leaveTypes = $this->leaveType::all();
        return LeaveTypeDTO::fromCollection($leaveTypes);
    }

    public function get($id): LeaveType
    {
        return $this->leaveType::findOrFail($id);
    }

    public function create(LeaveTypeDTO $leaveTypeDTO): LeaveType
    {
        $attributes = $leaveTypeDTO->toArray();
        $user = new LeaveType($attributes);
        $user->save();
        return $user;
    }

    public function update(int $userId, LeaveTypeDTO $userData): LeaveType
    {
        $leaveType = $this->leaveType->findOrFail($userId);
        $attributes = $userData->toArray();
        $leaveType->update($attributes);
        return $leaveType;
    }

    public function delete(int $id)
    {
        return $this->leaveType::findOrFail($id)->delete();
    }

    public function draw($data): StarterPaginator
    {
        //        $paginatedUsers = $this->prepareDatatableQuery($data, [User::ADMIN, User::EDITOR, User::COLLABORATOR]);

        $query = $this->leaveType->query();

        // $query->whereIn('roles.name', $roles);

        if (array_key_exists($data['column'], self::COLUMNS_MAP)) {
            $query->orderBy(self::COLUMNS_MAP[$data['column']], $data['dir']);
        }

        $search = $data['search'];
        if ($search) {
            $query->where(function ($subquery) use ($search) {
                $subquery->where('leave_types.slug', 'like', '%' . $search . '%');
                $subquery->orWhere('leave_types.name', 'like', '%' . $search . '%');
            });
        }

        $query->whereNull('deleted_at');

        return $query->paginate($data['length']);
    }
}
