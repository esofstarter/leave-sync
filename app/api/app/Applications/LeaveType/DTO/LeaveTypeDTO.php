<?php

namespace App\Applications\LeaveType\DTO;

use App\Applications\LeaveType\Model\LeaveType;
use Illuminate\Http\Request;

class LeaveTypeDTO
{
    public string $name;
    public string $slug;
    public string $color;
    public bool $is_paid;
    public int $id;

    public function __construct(
        string $name,
        string $slug,
        string $color,
        bool $is_paid = false,
        int $id = 0,
    ) {
        $this->name = $name;
        $this->slug = $slug;
        $this->color = $color;
        $this->is_paid = $is_paid;
        $this->id = $id;
    }

    public static function fromRequest(Request $request): self
    {
        return new self(
            $request->input('name'),
            $request->input('slug'),
            $request->input('color'),
            (bool) $request->input('is_paid', false),
            $request->input('id', 0),
        );
    }

    public static function fromRequestForCreate(Request $request): self
    {
        return new self(
            $request->input('name'),
            $request->input('slug'),
            $request->input('color'),
            is_paid: false,
            id: 0,
        );
    }

    public static function fromModel(LeaveType $leaveType): self
    {
        return new self(
            $leaveType->name,
            $leaveType->slug,
            $leaveType->color,
            (bool) $leaveType->is_paid,
            $leaveType->id,

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
            'slug' => $this->slug,
            'color' => $this->color,
            'is_paid' => $this->is_paid,
            'id' => $this->id,
        ];
    }

    public static function fromCollection(iterable $leaveTypes): array
    {
        return array_map(function (LeaveType $leaveType) {
            return self::fromModel($leaveType);
        }, $leaveTypes->all());
    }
}
