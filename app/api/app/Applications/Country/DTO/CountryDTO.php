<?php

namespace App\Applications\Country\DTO;

use App\Applications\Country\Model\Country;
use Illuminate\Http\Request;

class CountryDTO
{
    public string $name;
    public string $country_code;
    public int $id;


    public function __construct(
        string $name,
        string $country_code,
        int $id = 0,
    ) {
        $this->name = $name;
        $this->country_code = $country_code;
        $this->id = $id;
    }

    public static function fromRequest(Request $request): self
    {
        return new self(
            $request->input('name'),
            $request->input('country_code'),
            $request->input('id', 0),
        );
    }

    public static function fromRequestForCreate(Request $request): self
    {
        return new self(
            $request->input('name'),
            $request->input('country_code'),
            id: 0,
        );
    }

    public static function fromModel(Country $country): self
    {
        return new self(
            $country->name,
            $country->country_code,
            $country->id
        );
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'country_code' => $this->country_code,
            'id' => $this->id,
        ];
    }

    public static function fromCollection(iterable $countries): array
    {
        return array_map(function (Country $country) {
            return self::fromModel($country);
        }, $countries->all());
    }
}
