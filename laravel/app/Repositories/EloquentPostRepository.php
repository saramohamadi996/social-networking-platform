<?php

namespace App\Repositories;

use App\Models\Post;

class EloquentPostRepository implements PostRepositoryInterface
{
    /**
     * Retrieve posts by title.
     * @param $title
     * @return mixed
     */
    public function getByTitle($title): mixed
    {
        return Post::where('title', 'like', "%$title%")->get();
    }

    /**
     * Create a new post.
     * @param array $attributes
     * @return mixed
     */
    public function create(array $attributes): mixed
    {
        return Post::create($attributes);
    }

    /**
     * Update an existing post.
     * @param Post $post
     * @param array $attributes
     * @return Post
     */
    public function update(Post $post, array $attributes): Post
    {
        $post->update($attributes);
        return $post;
    }

    /**
     * Delete a post.
     * @param Post $post
     * @return void
     */
    public function delete(Post $post): void
    {
        $post->delete();
    }

    /**
     * Find a post by its ID.
     * @param $id
     * @return mixed
     */
    public function find($id): mixed
    {
        return Post::find($id);
    }
}

