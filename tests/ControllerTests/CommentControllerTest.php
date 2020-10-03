<?php

namespace AssignmentTwoTests\ControllerTests;

use AssignmentTwo\Controllers\CommentController;
use AssignmentTwo\Router\Request;
use AssignmentTwo\Router\Response;
use AssignmentTwoTests\AssignmentTwoTest;

final class CommentControllerTest extends AssignmentTwoTest
{
	public function testCommentControllerCalledShow(): void
	{
		$comment = $this->generateComment();

		$request = $this->createMock(Request::class);
		$request->method('getRequestMethod')->willReturn('GET');
		$request->method('getParameters')->willReturn([
			'body' => [],
			'header' => [$comment->getId()]
		]);

		$controller = new CommentController($request);

		$this->assertEquals('show', $controller->getAction());

		$response = $controller->doAction();

		$this->assertInstanceOf(Response::class, $response);
		$this->assertEquals('Comment was retrieved successfully!', $response->getMessage());
		$this->assertEquals($comment->getUser()->getId(), $response->getPayload()->getUser()->getId());
		$this->assertEquals($comment->getPost()->getId(), $response->getPayload()->getPost()->getId());
		$this->assertEquals($comment->getContent(), $response->getPayload()->getContent());
	}

	public function testCommentControllerCalledNew(): void
	{
		$comment = $this->generateCommentData();

		$request = $this->createMock(Request::class);
		$request->method('getRequestMethod')->willReturn('POST');
		$request->method('getParameters')->willReturn([
			'body' => [
				'postId' => $comment['postId'],
				'userId' => $comment['userId'],
				'content' => $comment['content'],
				'replyId' => $comment['replyId']
			],
			'header' => []
		]);

		$controller = new CommentController($request);

		$this->assertEquals('new', $controller->getAction());

		$response = $controller->doAction();

		$this->assertInstanceOf(Response::class, $response);
		$this->assertEquals('Comment was created successfully!', $response->getMessage());
		$this->assertEquals($comment['userId'], $response->getPayload()->getUser()->getId());
		$this->assertEquals($comment['content'], $response->getPayload()->getContent());
		$this->assertNotEmpty($response->getPayload()->getId());
	}

	public function testCommentControllerCalledEdit(): void
	{
		$comment = $this->generateComment();
		$newCommentData = $this->generateCommentData();

		$request = $this->createMock(Request::class);
		$request->method('getRequestMethod')->willReturn('PUT');
		$request->method('getParameters')->willReturn([
			'body' => [
				'content' => $newCommentData['content'],
			],
			'header' => [$comment->getId()]
		]);

		$controller = new CommentController($request);

		$this->assertEquals('edit', $controller->getAction());

		$response = $controller->doAction();

		$this->assertInstanceOf(Response::class, $response);
		$this->assertEquals('Comment was updated successfully!', $response->getMessage());
		$this->assertEquals($newCommentData['content'], $response->getPayload()->getContent());
		$this->assertNotEquals($comment->getContent(), $response->getPayload()->getUser()->getId());
	}

	public function testCommentControllerCalledDestroy(): void
	{
		$comment = $this->generateComment();

		$request = $this->createMock(Request::class);
		$request->method('getRequestMethod')->willReturn('DELETE');
		$request->method('getParameters')->willReturn([
			'body' => [],
			'header' => [$comment->getId()]
		]);

		$controller = new CommentController($request);

		$this->assertEquals('destroy', $controller->getAction());

		$response = $controller->doAction();

		$this->assertInstanceOf(Response::class, $response);
		$this->assertEquals('Comment was deleted successfully!', $response->getMessage());
		$this->assertEquals($comment->getUser()->getId(), $response->getPayload()->getUser()->getId());
		$this->assertEquals($comment->getPost()->getId(), $response->getPayload()->getPost()->getId());
		$this->assertEquals($comment->getContent(), $response->getPayload()->getContent());
	}
}
