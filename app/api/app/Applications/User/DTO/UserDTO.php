<?php

namespace App\Applications\User\DTO;

use App\Applications\User\Model\User;
use Illuminate\Http\Request;

class UserDTO
{
    public string $first_name;
    public string $last_name;
    public string $email;
    public ?string $private_id;
    public ?string $position;
    public ?string $avatar_url;
    public ?string $avatar_thumbnail;
    public int $role;
    public int $id;
    public bool $is_disabled;
    public array $permissions_array;
    public int $paid_leaves_max;
    public int $paid_leaves_left;
    public int $country;
    public bool $is_office_based;

    public function __construct(
        string $first_name,
        string $last_name,
        string $email,
        string $private_id = null,
        string $position = null,
        ?string $avatar_url,
        ?string $avatar_thumbnail,
        int $role,
        int $id = 0,
        bool $is_disabled,
        array $permissions_array = [],
        int $paid_leaves_max,
        int $paid_leaves_left,
        int $country,
        bool $is_office_based,
    ) {
        $this->first_name = $first_name;
        $this->last_name = $last_name;
        $this->email = $email;
        $this->private_id = $private_id;
        $this->position = $position;
        $this->avatar_url = $avatar_url;
        $this->avatar_thumbnail = $avatar_thumbnail;
        $this->role = $role;
        $this->id = $id;
        $this->is_disabled = $is_disabled;
        $this->permissions_array = $permissions_array;
        $this->paid_leaves_max = $paid_leaves_max;
        $this->paid_leaves_left = $paid_leaves_left;
        $this->country = $country;
        $this->is_office_based = $is_office_based;
    }

    public static function fromRequest(Request $request): self
    {
        return new self(
            $request->input('first_name'),
            $request->input('last_name'),
            $request->input('email'),
            $request->input('private_id'),
            $request->input('position'),
            null,
            null,
            $request->input('role'),
            $request->input('id', 0),
            (bool) $request->input('is_disabled', false),
            $request->input('permissions_array', []),
            $request->input('paid_leaves_max'),
            $request->input('paid_leaves_left'),
            $request->input('country'),
            (bool) $request->input('is_office_based', false)
        );
    }

    public static function fromRequestForCreate(Request $request): self
    {
        return new self(
            $request->input('first_name'),
            $request->input('last_name'),
            $request->input('email'),
            $request->input('private_id'),
            $request->input('position'),
            null,
            null,
            $request->input('role'),
            id: 0,
            is_disabled: $request->input('is_disabled', false),
            permissions_array: $request->input('permissions_array', []),
            paid_leaves_max: 0,
            paid_leaves_left: 0,
            country: $request->input('country'),
            is_office_based: $request->input('is_office_based', false)
        );
    }

    public static function fromModel(User $user): self
    {
        return new self(
            $user->first_name,
            $user->last_name,
            $user->email,
            $user->private_id,
            $user->position,
            $user->avatar_url,
            $user->avatar_thumbnail,
            $user->role,
            $user->id,
            $user->is_disabled,
            $user->permissions_array,
            $user->paid_leaves_max,
            $user->paid_leaves_left,
            $user->country,
            $user->is_office_based
        );
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function toArray(): array
    {
        return [
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'private_id' => $this->private_id,
            'position' => $this->position,
            'avatar_url' => $this->avatar_url,
            'avatar_thumbnail' => $this->avatar_thumbnail,
            'role' => $this->role,
            'id' => $this->id,
            'is_disabled' => $this->is_disabled,
            'permissions_array' => $this->permissions_array,
            'paid_leaves_max' => $this->paid_leaves_max,
            'paid_leaves_left' => $this->paid_leaves_left,
            'country' => $this->country,
            'is_office_based' => $this->is_office_based
        ];
    }

    public static function fromCollection(iterable $users): array
    {
        return array_map(function (User $user) {
            return self::fromModel($user);
        }, $users->all());
    }
}
