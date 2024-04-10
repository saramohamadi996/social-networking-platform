<?php

namespace App\Repositories;

use App\Models\Post;

interface PostRepositoryInterface
{
    /**
     * Retrieve posts by title.
     * @param $title
     */
    public function getByTitle($title);

    /**
     * Create a new post.
     * @param array $attributes
     */
    public function create(array $attributes);

    /**
     * Update an existing post.
     * @param Post $post
     * @param array $attributes
     */
    public function update(Post $post, array $attributes);

    /**
     * Delete a post.
     * @param Post $post
     */
    public function delete(Post $post);

    /**
     * Find a post by its ID
     * @param $id
     */
    public function find($id);
}

