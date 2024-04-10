<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Collection;

interface CommentRepositoryInterface
{
    /**
     * Get comments by name.
     * @param string $name
     * @return Collection
     */
    public function getByName(string $name): Collection;

    /**
     * Find a comment by its ID.
     * @param int $id
     * @return mixed
     */
    public function findById(int $id): mixed;

    /**
     * Create a new comment.
     * @param array $data
     * @return mixed
     */
    public function create(array $data): mixed;

    /**
     * Delete a comment by its ID.
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;
}
