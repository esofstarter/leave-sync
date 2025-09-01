<?php

namespace App\Applications\Document\Repositories;

use App\Applications\Pagination\StarterPaginator;
use App\Applications\Document\DTO\DocumentDTO;
use App\Applications\Document\Model\Document;

/**
 * Interface DocumentRepositoryInterface
 * @package App\Applications\Document
 */

interface DocumentRepositoryInterface
{
    /**
     * @return array
     */
    public function getAll(): array;

    /**
     * @param integer $id
     * @return Document
     */
    public function get($id);

    /**
     * @param array $data
     * @return StarterPaginator
     */
    public function draw(array $data): StarterPaginator;

}
