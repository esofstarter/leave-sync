<?php

namespace App\Applications\Country\Repositories;

use App\Applications\Country\DTO\CountryDTO;
use App\Applications\Pagination\StarterPaginator;
use App\Applications\Country\Model\Country;


/**
 * @property Country $country
 */
class CountryRepository implements CountryRepositoryInterface
{
    public function __construct(
        Country $country,
    ) {
        $this->country = $country;
    }

    private const COLUMNS_MAP = [
        'country_code' => 'countries.country_code',
        'name' => 'countries.name',
    ];

    public function getAll(): array
    {
        $countrys = $this->country::all();
        return CountryDTO::fromCollection($countrys);
    }

    public function get($id): Country
    {
        return $this->country::findOrFail($id);
    }

    public function create(CountryDTO $countryDTO): Country
    {
        $attributes = $countryDTO->toArray();
        $user = new Country($attributes);
        $user->save();
        return $user;
    }

    public function update(int $userId, CountryDTO $userData): Country
    {
        $country = $this->country->findOrFail($userId);
        $attributes = $userData->toArray();
        $country->update($attributes);
        return $country;
    }

    public function delete(int $id)
    {
        return $this->country::findOrFail($id)->delete();
    }

    public function draw($data): StarterPaginator
    {
        //        $paginatedUsers = $this->prepareDatatableQuery($data, [User::ADMIN, User::EDITOR, User::COLLABORATOR]);

        $query = $this->country->query();

        // $query->whereIn('roles.name', $roles);

        if (array_key_exists($data['column'], self::COLUMNS_MAP)) {
            $query->orderBy(self::COLUMNS_MAP[$data['column']], $data['dir']);
        }

        $search = $data['search'];
        if ($search) {
            $query->where(function ($subquery) use ($search) {
                $subquery->where('countries.country_code', 'like', '%' . $search . '%');
                $subquery->orWhere('countries.name', 'like', '%' . $search . '%');
            });
        }

        $query->whereNull('deleted_at');

        return $query->paginate($data['length']);
    }
}
