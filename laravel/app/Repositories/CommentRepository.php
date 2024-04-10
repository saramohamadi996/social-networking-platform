<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Collection;
use App\Models\Comment;

class CommentRepository implements CommentRepositoryInterface
{
    /**
     * Get comments by name.
     * @param string $name
     * @return Collection
     */
    public function getByName(string $name): Collection
    {
        return Comment::whereHas('user', function ($query) use ($name) {
            $query->where('name', $name);
        })->get();
    }

    /**
     * Find a comment by its ID.
     * @param int $id
     * @return mixed
     */
    public function findById(int $id): mixed
    {
        return Comment::with('replies')->find($id);
    }

    /**
     * Create a new comment.
     * @param array $data
     * @return mixed
     */
    public function create(array $data): mixed
    {
        return Comment::create($data);
    }

    /**
     * Delete a comment by its ID.
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $comment = Comment::find($id);
        if ($comment) {
            $comment->delete();
            return true;
        }
        return false;
    }
}
