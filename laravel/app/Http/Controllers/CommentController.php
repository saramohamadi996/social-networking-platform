<?php

namespace App\Http\Controllers;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Http\Requests\CommentSearchRequest;
use App\Http\Requests\CommentStoreRequest;
use App\Http\Response\ResponseGenerator;
use App\Http\Resources\CommentResource;
use Illuminate\Http\JsonResponse;
use App\Services\CommentService;

class CommentController extends Controller
{
    protected CommentService $commentService;

    public function __construct(CommentService $comment_service)
    {
        $this->commentService = $comment_service;
    }

    /**
     * Retrieve a list of comments.
     * @param CommentSearchRequest $request
     * @return AnonymousResourceCollection
     */
    public function index(CommentSearchRequest $request): AnonymousResourceCollection
    {
        $comments = $this->commentService->index($request->name)->with('user')->get();
        return CommentResource::collection($comments);
    }

    /**
     * Store a newly created comment.
     * @param CommentStoreRequest $request
     * @return JsonResponse
     */
    public function store(CommentStoreRequest $request): JsonResponse
    {
        $validated_data = $request->validated();
        $comment = $this->commentService->store($validated_data);
        return ResponseGenerator::created('Comment created successfully', new CommentResource($comment));
    }

    /**
     * Store a reply to a comment.
     * @param int $parent_id
     * @param CommentStoreRequest $request
     * @return JsonResponse
     */
    public function storeReply(int $parent_id, CommentStoreRequest $request): JsonResponse
    {
        $parent_comment = $this->commentService->show($parent_id);
        if (!$parent_comment) {
            return ResponseGenerator::notFound('Parent comment not found');
        }

        $validated_data = $request->validated();
        $reply = $this->commentService->storeReply($parent_id, $validated_data);

        if ($reply) {
            return ResponseGenerator::success('Comment reply created successfully', new CommentResource($reply));
        } else {
            return ResponseGenerator::error('Failed to create comment reply');
        }
    }

    /**
     * Display the specified comment.
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $comment = $this->commentService->show($id);
        if (!$comment) {
            return ResponseGenerator::notFound('Comment not found');
        }
        return ResponseGenerator::success('Comment retrieved successfully', new CommentResource($comment));
    }


    /**
     * Remove the specified comment from storage.
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        if ($this->commentService->destroy($id)) {
            return ResponseGenerator::success('Comment deleted successfully', []);
        }
        return ResponseGenerator::notFound('Comment not found');
    }

}
