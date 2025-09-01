<?php

namespace App\Applications\Document\DTO;

use App\Applications\Document\Model\Document;
use Illuminate\Http\Request;

class DocumentDTO
{
    public string $file_name;
    public string $file_path;
    public int $id;
    public int $leave_request_id;
    public int $user_id;

    public function __construct(
        string $file_name,
        string $file_path,
        int $id = 0,
        int $leave_request_id = 0,
        int $user_id = 0,
    ) {
        $this->file_name = $file_name;
        $this->file_path = $file_path;
        $this->id = $id;
        $this->leave_request_id = $leave_request_id;
        $this->user_id = $user_id;
    }

    public static function fromRequest(Request $request): self
    {
        return new self(
            $request->input('file_name'),
            $request->input('file_path'),
            $request->input('id', 0),
            $request->input('leave_request_id', 0),
            $request->input('user_id', 0),
        );
    }

    public static function fromRequestForCreate(Request $request): self
    {
        return new self(
            $request->input('file_name'),
            $request->input('file_path'),
            id: 0,
            leave_request_id: 0,
            user_id: 0,
        );
    }

    public static function fromModel(Document $document): self
    {
        return new self(
            $document->file_name,
            $document->file_path,
            $document->id,
            $document->leave_request_id,
            $document->user_id,

        );
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function toArray(): array
    {
        return [
            'name' => $this->file_name,
            'slug' => $this->file_path,
            'id' => $this->id,
            'leave_request_id' => $this->leave_request_id,
            'user_id' => $this->user_id,
        ];
    }

    public static function fromCollection(iterable $leaveTypes): array
    {
        return array_map(function (Document $leaveType) {
            return self::fromModel($leaveType);
        }, $leaveTypes->all());
    }
}
