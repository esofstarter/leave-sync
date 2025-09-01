<?php

namespace App\Applications\Document\Repositories;

use App\Applications\Document\DTO\DocumentDTO;
use App\Applications\Pagination\StarterPaginator;
use App\Applications\Document\Model\Document;


/**
 * @property Document $document
 */
class DocumentRepository implements DocumentRepositoryInterface
{
    public function __construct(
        Document $document,
    ) {
        $this->document = $document;
    }

    private const COLUMNS_MAP = [
        'file_name' => 'documents.file_name',
        'file_path' => 'documents.file_path',
    ];

    public function getAll(): array
    {
        $documents = $this->document::all();
        return DocumentDTO::fromCollection($documents);
    }

    public function get($id): Document
    {
        return $this->document::findOrFail($id);
    }

    public function draw($data): StarterPaginator
    {
        //        $paginatedUsers = $this->prepareDatatableQuery($data, [User::ADMIN, User::EDITOR, User::COLLABORATOR]);
        $query = $this->document->query();

        // $query->whereIn('roles.name', $roles);

        if (array_key_exists($data['column'], self::COLUMNS_MAP)) {
            $query->orderBy(self::COLUMNS_MAP[$data['column']], $data['dir']);
        }

        $userId = $data['userId'];
        if ($userId ) {
            $query->where('documents.user_id', '=', $userId);
        }

        return $query->paginate($data['length']);
    }
}
