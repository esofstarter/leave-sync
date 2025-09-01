<?php

namespace App\Applications\Document\Services;

use App\Applications\Document\DTO\DocumentDTO;
use App\Applications\Document\Repositories\DocumentRepositoryInterface;

/**
 * @property DocumentRepositoryInterface $documentRepository
 */
class DocumentService implements DocumentServiceInterface
{
    public function __construct(
        DocumentRepositoryInterface $documentRepository
    ) {
        $this->documentRepository = $documentRepository;
    }

    public function getAll(): array
    {
        return $this->documentRepository->getAll();
    }

    public function get($id): DocumentDTO
    {
        return DocumentDTO::fromModel(
            $this->documentRepository->get($id)
        );
    }

    public function draw(array $data): array
    {
        $data['columns'] = ['users.first_name', 'users.last_name', 'email', 'roles.id', 'users.is_disabled'];
        $data['length'] = $data['length'] ?? 25;
        $data['column'] = $data['column'] ?? 'users.first_name';
        $data['dir'] = $data['dir'] ?? 'asc';
        $data['search'] = $data['search'] ?? '';
        $data['userId'] = $data['userId'] ?? '';
        $data['draw'] = $data['draw'] ?? 1;

        $usersCollection = $this->documentRepository->draw($data);
        $usersDTOs = $usersCollection->getCollection()->map(function ($user) {
            return DocumentDTO::fromModel($user);
        });

        return [
            'data' => $usersDTOs,
            'pagination' => $usersCollection->toArray()['pagination'],
        ];
    }
}
