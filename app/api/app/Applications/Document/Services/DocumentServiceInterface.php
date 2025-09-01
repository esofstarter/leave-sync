<?php

namespace App\Applications\Document\Services;

use App\Applications\Document\DTO\DocumentDTO;
use Illuminate\Http\Request;

/**
 * Interface DocumentServiceInterface
 * @package App\Applications\Document
 */

interface DocumentServiceInterface
{
    /**
     * @return array
     */
    public function getAll(): array;

    /**
     * @param integer $id
     * @return DocumentDTO
     */
    public function get(int $id): DocumentDTO;

    /**
     * @param array $data
     * @return array
     */
    public function draw(array $data): array;
}
