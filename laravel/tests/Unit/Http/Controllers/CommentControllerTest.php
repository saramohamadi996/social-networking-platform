<?php

namespace Tests\Unit\Http\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Foundation\Testing\WithFaker;
use App\Http\Controllers\CommentController;
use App\Http\Requests\CommentStoreRequest;
use App\Http\Resources\CommentResource;
use Illuminate\Http\JsonResponse;
use App\Services\CommentService;
use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Tests\TestCase;
use Mockery;

class CommentControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->commentServiceMock = Mockery::mock(CommentService::class);
        $this->app->instance(CommentService::class, $this->commentServiceMock);
        $this->commentService = Mockery::mock(CommentService::class);
        $this->controller = new CommentController($this->commentService);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    /**
     * Test if the index method returns a collection of comments.
     * @return void
     */
    public function test_index_method_returns_collection_of_comments()
    {
        $user = User::factory()->create([
            'password' => Hash::make('password'),
        ]);

        $token = $user->createToken('TestToken')->plainTextToken;

        $comments = Comment::factory()->count(3)->create();
        $comment_resource_collection = CommentResource::collection($comments);

        $this->commentServiceMock->shouldReceive('index')
            ->once()
            ->andReturn($comments);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson(route('comments.index'));

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson($comment_resource_collection->response()->getData(true));
    }

    /**
     * Test if the store method creates a new comment.
     * @return void
     */
    public function test_store_method_creates_new_comment()
    {
        $user = User::factory()->create();
        $token = $user->createToken('Test Token')->plainTextToken;
        $post = Post::factory()->create();
        $comment_data = [
            'user_id' => $user->id,
            'post_id' => $post->id,
            'content' => 'Test comment content'
        ];
        $this->commentServiceMock->shouldReceive('store')
            ->once()
            ->with($comment_data)
            ->andReturn(new Comment($comment_data));
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('POST', route('comments.store'), $comment_data);
        $response->assertStatus(200);
    }

    /**
     * Test storing a reply with a valid parent ID and request.
     * @return void
     */
    public function test_store_reply_with_valid_parent_id_and_request(): void
    {
        $parent_id = 1;
        $parent_comment = Comment::factory()->create(['id' => $parent_id]);

        $validated_data = ['content' => 'This is a reply'];
        $request = Mockery::mock(CommentStoreRequest::class);
        $request->shouldReceive('validated')->andReturn($validated_data);

        $this->commentService->shouldReceive('show')->with($parent_id)->andReturn($parent_comment);

        $reply_comment = Comment::factory()->make($validated_data);
        $this->commentService->shouldReceive('storeReply')->with($parent_id, $validated_data)->andReturn($reply_comment);

        $response = $this->controller->storeReply($parent_id, $request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $response_data = $response->getData(true);
        $this->assertEquals('success', $response_data['status']);
        $response_dataWithoutExtraKeys = array_diff_key($response_data['data'], array_flip(['id']));
        $this->assertEquals($reply_comment->toArray(), $response_dataWithoutExtraKeys);
    }

    /**
     * Test storing a reply with an invalid parent ID.
     * @return void
     */
    public function test_store_reply_with_invalid_parent_id(): void
    {
        $parent_id = 999;
        $this->commentService->shouldReceive('show')->with($parent_id)->andReturn(null);

        $response = $this->controller->storeReply($parent_id, Mockery::mock(CommentStoreRequest::class));

        $this->assertInstanceOf(JsonResponse::class, $response);
        $response_data = $response->getData(true);
        $this->assertEquals('error', $response_data['status']);
        $this->assertEquals('Parent comment not found', $response_data['message']);
    }

    /**
     * Test showing an existing comment.
     * @return void
     */
    public function test_show_existing_comment(): void
    {
        $comment_id = 1;
        $comment = Comment::factory()->create(['id' => $comment_id]);
        $comment_resource = new CommentResource($comment);

        $this->commentService
            ->shouldReceive('show')
            ->with($comment_id)
            ->andReturn($comment);

        $response = $this->controller->show($comment_id);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $response_data = $response->getData(true);
        $this->assertEquals('success', $response_data['status']);
        $this->assertEquals($comment_resource->toArray(new Request()), $response_data['data']);
    }

    /**
     * Test showing a non-existing comment.
     * @return void
     */
    public function test_show_non_existing_comment(): void
    {
        $non_existing_comment_id = 999;

        $this->commentService
            ->shouldReceive('show')
            ->with($non_existing_comment_id)
            ->andReturn(null);

        $response = $this->controller->show($non_existing_comment_id);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $response_data = $response->getData(true);
        $this->assertEquals('error', $response_data['status']);
        $this->assertEquals('Comment not found', $response_data['message']);
    }

    /**
     * Test destroying an existing comment.
     * @return void
     */
    public function test_destroy_existing_comment(): void
    {
        $comment_id = 1;

        $this->commentService
            ->shouldReceive('destroy')
            ->with($comment_id)
            ->andReturn(true);

        $response = $this->controller->destroy($comment_id);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $response_data = $response->getData(true);
        $this->assertEquals('success', $response_data['status']);
        $this->assertEquals([], $response_data['data']);
        $this->assertEquals('Comment deleted successfully', $response_data['message']);
    }

    /**
     * Test destroying a non-existing comment.
     * @return void
     */
    public function test_destroy_non_existing_comment(): void
    {
        $non_existing_comment_id = 999;

        $this->commentService
            ->shouldReceive('destroy')
            ->with($non_existing_comment_id)
            ->andReturn(false);

        $response = $this->controller->destroy($non_existing_comment_id);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $response_data = $response->getData(true);
        $this->assertEquals('error', $response_data['status']);
        $this->assertEquals('Comment not found', $response_data['message']);
    }

}
