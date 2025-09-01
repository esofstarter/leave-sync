<?php

namespace App\Applications\NationalHoliday\Services;

use App\Applications\NationalHoliday\DTO\NationalHolidayDTO;
use App\Applications\NationalHoliday\Repositories\NationalHolidayRepositoryInterface;

/**
 * @property NationalHolidayRepositoryInterface $nationalHolidayRepository
 */
class NationalHolidayService implements NationalHolidayServiceInterface
{
    public function __construct(
        NationalHolidayRepositoryInterface $nationalHolidayRepository
    ) {
        $this->nationalHolidayRepository = $nationalHolidayRepository;
    }

    public function getAll(): array
    {
        return $this->nationalHolidayRepository->getAll();
    }

    public function get($id): NationalHolidayDTO
    {
        return NationalHolidayDTO::fromModel(
            $this->nationalHolidayRepository->get($id)
        );
    }

    public function create(NationalHolidayDTO $leaveTpyeData): NationalHolidayDTO
    {
        $leaveType = $this->nationalHolidayRepository->create($leaveTpyeData);
        return NationalHolidayDTO::fromModel($leaveType);
    }

    public function update(int $nationalHolidayId, NationalHolidayDTO $leaveTpyeData): NationalHolidayDTO
    {
        $leaveType = $this->nationalHolidayRepository->update($nationalHolidayId, $leaveTpyeData);
        return NationalHolidayDTO::fromModel($leaveType);
    }

    public function delete(int $id)
    {
        return $this->nationalHolidayRepository->delete($id);
    }

    public function draw(array $data): array
    {
        $data['columns'] = ['national_holidays.date', 'national_holidays.country'];
        $data['length'] = $data['length'] ?? 25;
        $data['column'] = $data['column'] ?? 'national_holidays.country';
        $data['dir'] = $data['dir'] ?? 'asc';
        $data['isCountry'] = $data['isCountry'] ?? '';
        $data['search'] = $data['search'] ?? '';
        $data['draw'] = $data['draw'] ?? 1;

        $usersCollection = $this->nationalHolidayRepository->draw($data);
        $usersDTOs = $usersCollection->getCollection()->map(function ($user) {
            return NationalHolidayDTO::fromModel($user);
        });

        return [
            'data' => $usersDTOs,
            'pagination' => $usersCollection->toArray()['pagination'],
        ];
    }
}
