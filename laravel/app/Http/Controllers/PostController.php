<?php

namespace App\Http\Controllers;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Http\Response\ResponseGenerator;
use App\Http\Requests\PostUpdateRequest;
use App\Http\Requests\PostSearchRequest;
use App\Http\Requests\PostStoreRequest;
use Illuminate\Support\Facades\Cache;
use App\Http\Resources\PostResource;
use Illuminate\Http\JsonResponse;
use App\Services\PostService;

class PostController extends Controller
{
    protected PostService $postService;

    public function __construct(PostService $post_service)
    {
        $this->postService = $post_service;
    }

    /**
     * Retrieve a list of posts filtered by title.
     * @param PostSearchRequest $request
     * @return AnonymousResourceCollection
     */
    public function index(PostSearchRequest $request): AnonymousResourceCollection
    {
        $key = 'posts_' . $request->title;
        if (Cache::has($key)) {
            $posts = Cache::get($key);
        } else {
            $title = $request->input('title');
            $posts = $this->postService->filterByTitle($title);
            Cache::put($key, $posts, now()->addMinutes(10));
        }

        return PostResource::collection($posts);
    }

    /**
     * Store a newly created post.
     * @param PostStoreRequest $request
     * @return JsonResponse
     */
    public function store(PostStoreRequest $request): JsonResponse
    {
        $post = $this->postService->createPost($request->validated());
        return ResponseGenerator::created('Post created successfully', new PostResource($post));
    }

    /**
     * Update the specified post.
     * @param PostUpdateRequest $request
     * @param $post_id
     * @return JsonResponse
     */
    public function update(PostUpdateRequest $request, $post_id): JsonResponse
    {
        $post = $this->postService->findPostById($post_id);
        if (!$post) {
            return ResponseGenerator::notFound('Post not found');
        }
        $this->postService->updatePost($post, $request->validated());
        return ResponseGenerator::success('Comment retrieved successfully', new PostResource($post));
    }

    /**
     * Remove the specified post from storage.
     * @param $post_id
     * @return JsonResponse
     */
    public function destroy($post_id): JsonResponse
    {
        $post = $this->postService->findPostById($post_id);
        if (!$post) {
            return ResponseGenerator::notFound('Post not found');
        }

        $this->postService->deletePost($post);
        return response()->json(null, 204);
    }
}
