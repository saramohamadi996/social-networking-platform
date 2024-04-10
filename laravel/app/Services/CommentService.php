<?php

namespace App\Services;

use App\Repositories\CommentRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class CommentService
{
    protected CommentRepositoryInterface $commentRepository;

    public function __construct(CommentRepositoryInterface $comment_repository)
    {
        $this->commentRepository = $comment_repository;
    }

    /**
     * Get comments by username.
     * @param string $name
     * @return Collection
     */
    public function index(string $name): Collection
    {
        return $this->commentRepository->getByName($name);
    }

    /**
     * Store a new comment.
     * @param array $data
     * @return mixed
     */
    public function store(array $data): mixed
    {
        return $this->commentRepository->create($data);
    }

    /**
     * Store a reply to a comment.
     * @param int $parent_id
     * @param array $data
     * @return mixed
     */
    public function storeReply(int $parent_id, array $data): mixed
    {
        $data['parent_id'] = $parent_id;
        return $this->commentRepository->create($data);
    }

    /**
     * Show a comment by its ID.
     * @param int $id
     * @return mixed
     */
    public function show(int $id): mixed
    {
        return $this->commentRepository->findById($id);
    }

    /**
     * Delete a comment by its ID.
     * @param int $id
     * @return bool
     */
    public function destroy(int $id): bool
    {
        return $this->commentRepository->delete($id);
    }
}
