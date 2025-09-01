<?php

namespace App\Applications\Country\Services;

use App\Applications\Country\DTO\CountryDTO;
use App\Applications\Country\Repositories\CountryRepositoryInterface;

/**
 * @property CountryRepositoryInterface $countryRepository
 */
class CountryService implements CountryServiceInterface
{
    public function __construct(
        CountryRepositoryInterface $countryRepository
    ) {
        $this->countryRepository = $countryRepository;
    }

    public function getAll(): array
    {
        return $this->countryRepository->getAll();
    }

    public function get($id): CountryDTO
    {
        return CountryDTO::fromModel(
            $this->countryRepository->get($id)
        );
    }

    public function create(CountryDTO $countryData): CountryDTO
    {
        $country = $this->countryRepository->create($countryData);
        return CountryDTO::fromModel($country);
    }

    public function update(int $countryId, CountryDTO $countryData): CountryDTO
    {
        $country = $this->countryRepository->update($countryId, $countryData);
        return CountryDTO::fromModel($country);
    }

    public function delete(int $id)
    {
        return $this->countryRepository->delete($id);
    }

    public function draw(array $data): array
    {
        $data['columns'] = ['users.first_name', 'users.last_name', 'email', 'roles.id', 'users.is_disabled'];
        $data['length'] = $data['length'] ?? 10;
        $data['column'] = $data['column'] ?? 'users.first_name';
        $data['dir'] = $data['dir'] ?? 'asc';
        $data['search'] = $data['search'] ?? '';
        $data['draw'] = $data['draw'] ?? 1;

        $usersCollection = $this->countryRepository->draw($data);

        $usersDTOs = $usersCollection->getCollection()->map(function ($user) {
            return CountryDTO::fromModel($user);
        });

        return [
            'data' => $usersDTOs,
            'pagination' => $usersCollection->toArray()['pagination'],
        ];
    }
}
