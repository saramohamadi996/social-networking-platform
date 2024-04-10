<?php

namespace Tests\Unit\Http\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Http\Requests\PostSearchRequest;
use App\Http\Requests\PostUpdateRequest;
use App\Http\Controllers\PostController;
use App\Services\PostService;
use App\Models\User;
use App\Models\Post;
use Tests\TestCase;
use Mockery;

class PostControllerTest extends TestCase
{
    use RefreshDatabase;
    protected Mockery\LegacyMockInterface|Mockery\MockInterface|PostService $postServiceMock;
    protected PostController $postController;
    public function setUp(): void
    {
        parent::setUp();
        $this->postServiceMock = Mockery::mock(PostService::class);
        $this->app->instance(PostService::class, $this->postServiceMock);
        $this->postController = new PostController($this->postServiceMock);
    }

    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * Test for the index method.
     * @return void
     */
    public function test_index_post_method()
    {
        $this->postServiceMock->shouldReceive('filterByTitle')->once()->andReturn(collect([new Post(), new Post()]));
        $response = $this->postController->index(new PostSearchRequest(['title' => 'example']));
        $this->assertCount(2, $response);
    }

    /**
     * Test for the store method.
     * @return void
     */
    public function test_store_post_method()
    {
        $user = User::factory()->create();
        $token = $user->createToken('Test Token')->plainTextToken;
        $post_data = [
            'user_id' => $user->id,
            'title' => 'Test Post',
            'content' => 'Test content'
        ];

        $this->postServiceMock
            ->shouldReceive('createPost')
            ->once()
            ->with($post_data)
            ->andReturn(new Post($post_data));

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('POST', '/api/posts', $post_data);

        $response->assertStatus(200);
    }

    /**
     * Test for the update method.
     * @return void
     */
    public function test_update_post_method()
    {
        $user = User::factory()->create();
        $token = $user->createToken('Test Token')->plainTextToken;

        $post_id = 1;
        $post_data = [
            'title' => 'Updated Post',
            'content' => 'Updated content',
            'likes_count' => 10,
            'shares_count' => 5,
            'view_count' => 100
        ];

        $updateRequest = new PostUpdateRequest();
        $updateRequest->merge($post_data);

        $this->postServiceMock->shouldReceive('findPostById')->once()->with($post_id)->andReturn(new Post());
        $this->postServiceMock->shouldReceive('updatePost')->once()->with(Mockery::type(Post::class), $post_data);
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('PUT', '/api/posts/' . $post_id, $post_data);

        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * Test for the destroy method.
     * @return void
     */
    public function test_destroy_post_method()
    {
        $this->postServiceMock->shouldReceive('findPostById')->once()->andReturn(new Post());
        $this->postServiceMock->shouldReceive('deletePost')->once();

        $response = $this->postController->destroy(1);

        $this->assertEquals(204, $response->getStatusCode());
    }

}
