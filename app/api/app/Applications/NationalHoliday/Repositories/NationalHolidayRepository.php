<?php

namespace App\Applications\NationalHoliday\Repositories;

use App\Applications\NationalHoliday\DTO\NationalHolidayDTO;
use App\Applications\Pagination\StarterPaginator;
use App\Applications\NationalHoliday\Model\NationalHoliday;


/**
 * @property NationalHoliday $leaveType
 */
class NationalHolidayRepository implements NationalHolidayRepositoryInterface
{
    public function __construct(
        NationalHoliday $leaveType,
    ) {
        $this->leaveType = $leaveType;
    }

    private const COLUMNS_MAP = [
        'date' => 'national_holidays.date',
        'country' => 'national_holidays.country',
        'year' => 'national_holidays.year',
    ];

    public function getAll(): array
    {
        $leaveTypes = $this->leaveType::all();
        return NationalHolidayDTO::fromCollection($leaveTypes);
    }

    public function get($id): NationalHoliday
    {
        return $this->leaveType::findOrFail($id);
    }

    public function create(NationalHolidayDTO $leaveTypeDTO): NationalHoliday
    {
        $attributes = $leaveTypeDTO->toArray();
        $user = new NationalHoliday($attributes);
        $user->save();
        return $user;
    }

    public function update(int $userId, NationalHolidayDTO $userData): NationalHoliday
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
                $subquery->where('national_holidays.country', 'like', '%' . $search . '%');
                $subquery->orWhere('national_holidays.date', 'like', '%' . $search . '%');
                $subquery->orWhere('national_holidays.year', 'like', '%' . $search . '%');
            });
        }

        $country = $data['isCountry'];
        if ($country == 1) {
            $query->where('national_holidays.country', 'like', '%Macedonia%');
        } else if ($country == 2) {
            $query->where('national_holidays.country', 'like', '%Bulgaria%');
        }

        return $query->paginate($data['length']);
    }
}
