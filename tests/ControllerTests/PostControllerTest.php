<?php

namespace AssignmentTwoTests\ControllerTests;

use AssignmentTwo\Controllers\PostController;
use AssignmentTwo\Router\Request;
use AssignmentTwo\Router\Response;
use AssignmentTwoTests\AssignmentTwoTest;

final class PostControllerTest extends AssignmentTwoTest
{
	public function testPostControllerCalledShow(): void
	{
		$post = $this->generatePost();

		$request = $this->createMock(Request::class);
		$request->method('getRequestMethod')->willReturn('GET');
		$request->method('getParameters')->willReturn([
			'body' => [],
			'header' => [$post->getId()]
		]);

		$controller = new PostController($request);

		$this->assertEquals('show', $controller->getAction());

		$response = $controller->doAction();

		$this->assertInstanceOf(Response::class, $response);
		$this->assertEquals('Post was retrieved successfully!', $response->getMessage());
		$this->assertEquals($post->getId(), $response->getPayload()->getId());
		$this->assertEquals($post->getTitle(), $response->getPayload()->getTitle());
		$this->assertEquals($post->getContent(), $response->getPayload()->getContent());
	}

	public function testPostControllerCalledNew(): void
	{
		$user = $this->generateUser();
		$category = $this->generateCategory();
		$post = $this->generatePostData();

		$request = $this->createMock(Request::class);
		$request->method('getRequestMethod')->willReturn('POST');
		$request->method('getParameters')->willReturn([
			'body' => [
				'userId' => $user->getId(),
				'categoryId' => $category->getId(),
				'title' => $post['title'],
				'type' => $post['type'],
				'content' => $post['content']
			],
			'header' => []
		]);

		$controller = new PostController($request);

		$this->assertEquals('new', $controller->getAction());

		$response = $controller->doAction();

		$this->assertInstanceOf(Response::class, $response);
		$this->assertEquals('Post was created successfully!', $response->getMessage());
		$this->assertEquals($post['title'], $response->getPayload()->getTitle());
		$this->assertEquals($post['content'], $response->getPayload()->getContent());
		$this->assertNotEmpty($response->getPayload()->getId());
	}

	public function testPostControllerCalledEdit(): void
	{
		$post = $this->generatePost(true);
		$newPostData = $this->generatePostData(true);

		$request = $this->createMock(Request::class);
		$request->method('getRequestMethod')->willReturn('PUT');
		$request->method('getParameters')->willReturn([
			'body' => [
				'content' => $newPostData['content'],
			],
			'header' => [$post->getId()]
		]);

		$controller = new PostController($request);

		$this->assertEquals('edit', $controller->getAction());

		$response = $controller->doAction();

		$this->assertInstanceOf(Response::class, $response);
		$this->assertEquals('Post was updated successfully!', $response->getMessage());
		$this->assertEquals($newPostData['content'], $response->getPayload()->getContent());
		$this->assertNotEquals($post->getContent(), $response->getPayload()->getContent());
	}

	public function testPostControllerCalledDestroy(): void
	{
		$post = $this->generatePost();

		$request = $this->createMock(Request::class);
		$request->method('getRequestMethod')->willReturn('DELETE');
		$request->method('getParameters')->willReturn([
			'body' => [],
			'header' => [$post->getId()]
		]);

		$controller = new PostController($request);

		$this->assertEquals('destroy', $controller->getAction());

		$response = $controller->doAction();

		$this->assertInstanceOf(Response::class, $response);
		$this->assertEquals('Post was deleted successfully!', $response->getMessage());
		$this->assertEquals($post->getId(), $response->getPayload()->getId());
		$this->assertEquals($post->getTitle(), $response->getPayload()->getTitle());
		$this->assertEquals($post->getContent(), $response->getPayload()->getContent());
	}
}
