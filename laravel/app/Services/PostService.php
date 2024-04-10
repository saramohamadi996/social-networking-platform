<?php

namespace App\Services;

use App\Models\Post;
use App\Repositories\PostRepositoryInterface;

class PostService
{
    protected PostRepositoryInterface $postRepository;

    public function __construct(PostRepositoryInterface $post_repository)
    {
        $this->postRepository = $post_repository;
    }

    /**
     * Retrieve posts by title.
     * @param $title
     * @return mixed
     */
    public function filterByTitle($title): mixed
    {
        return $this->postRepository->getByTitle($title);
    }

    /**
     * Create a new post.
     * @param array $attributes
     * @return mixed
     */
    public function createPost(array $attributes): mixed
    {
        return $this->postRepository->create($attributes);
    }

    /**
     * Update an existing post.
     * @param Post $post
     * @param array $attributes
     * @return mixed
     */
    public function updatePost(Post $post, array $attributes): mixed
    {
        return $this->postRepository->update($post, $attributes);
    }

    /**
     * Delete a post.
     * @param Post $post
     * @return void
     */
    public function deletePost(Post $post): void
    {
        $this->postRepository->delete($post);
    }

    /**
     * Find a post by its ID.
     * @param $post_id
     * @return mixed
     */
    public function findPostById($post_id): mixed
    {
        return $this->postRepository->find($post_id);
    }
}
