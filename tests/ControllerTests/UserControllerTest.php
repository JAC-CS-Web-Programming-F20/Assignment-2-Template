<?php

namespace AssignmentTwoTests\ControllerTests;

use AssignmentTwo\Controllers\UserController;
use AssignmentTwo\Router\Request;
use AssignmentTwo\Router\Response;
use AssignmentTwoTests\AssignmentTwoTest;

final class UserControllerTest extends AssignmentTwoTest
{
	public function testUserControllerCalledShow(): void
	{
		$user = $this->generateUser();

		$request = $this->createMock(Request::class);
		$request->method('getRequestMethod')->willReturn('GET');
		$request->method('getParameters')->willReturn([
			'body' => [],
			'header' => [$user->getId()]
		]);

		$controller = new UserController($request);

		$this->assertEquals('show', $controller->getAction());

		$response = $controller->doAction();

		$this->assertInstanceOf(Response::class, $response);
		$this->assertEquals('User was retrieved successfully!', $response->getMessage());
		$this->assertEquals($user->getId(), $response->getPayload()->getId());
		$this->assertEquals($user->getUsername(), $response->getPayload()->getUsername());
		$this->assertEquals($user->getEmail(), $response->getPayload()->getEmail());
	}

	public function testUserControllerCalledNew(): void
	{
		$user = $this->generateUserData();

		$request = $this->createMock(Request::class);
		$request->method('getRequestMethod')->willReturn('POST');
		$request->method('getParameters')->willReturn([
			'body' => [
				'username' => $user['username'],
				'email' => $user['email'],
				'password' => $user['password']
			],
			'header' => []
		]);

		$controller = new UserController($request);

		$this->assertEquals('new', $controller->getAction());

		$response = $controller->doAction();

		$this->assertInstanceOf(Response::class, $response);
		$this->assertEquals('User was created successfully!', $response->getMessage());
		$this->assertEquals($user['username'], $response->getPayload()->getUsername());
		$this->assertEquals($user['email'], $response->getPayload()->getEmail());
		$this->assertNotEmpty($response->getPayload()->getId());
	}

	public function testUserControllerCalledEdit(): void
	{
		$user = $this->generateUser();
		$newUserData = $this->generateUserData();

		$request = $this->createMock(Request::class);
		$request->method('getRequestMethod')->willReturn('PUT');
		$request->method('getParameters')->willReturn([
			'body' => [
				'username' => $newUserData['username'],
				'email' => $newUserData['email']
			],
			'header' => [$user->getId()]
		]);

		$controller = new UserController($request);

		$this->assertEquals('edit', $controller->getAction());

		$response = $controller->doAction();

		$this->assertInstanceOf(Response::class, $response);
		$this->assertEquals('User was updated successfully!', $response->getMessage());
		$this->assertEquals($newUserData['username'], $response->getPayload()->getUsername());
		$this->assertEquals($newUserData['email'], $response->getPayload()->getEmail());
		$this->assertNotEquals($user->getUsername(), $response->getPayload()->getUsername());
		$this->assertNotEquals($user->getEmail(), $response->getPayload()->getEmail());
	}

	public function testUserControllerCalledDestroy(): void
	{
		$user = $this->generateUser();

		$request = $this->createMock(Request::class);
		$request->method('getRequestMethod')->willReturn('DELETE');
		$request->method('getParameters')->willReturn([
			'body' => [],
			'header' => [$user->getId()]
		]);

		$controller = new UserController($request);

		$this->assertEquals('destroy', $controller->getAction());

		$response = $controller->doAction();

		$this->assertInstanceOf(Response::class, $response);
		$this->assertEquals('User was deleted successfully!', $response->getMessage());
		$this->assertEquals($user->getId(), $response->getPayload()->getId());
		$this->assertEquals($user->getUsername(), $response->getPayload()->getUsername());
		$this->assertEquals($user->getEmail(), $response->getPayload()->getEmail());
	}

	// public function testUserCommentsWereRetrieved(): void
	// {
	// 	$user = $this->generateUser();

	// 	for ($i = 0; $i < rand(1, 20); $i++) {
	// 		$posts[] = $this->generatePost();

	// 		// User will randomly comment on posts.
	// 		if (rand(0, 1) === 0) {
	// 			$comments[] = $this->generateComment($posts[$i]->getId(), $user->getId());
	// 		}
	// 	}

	// 	// Force a comment in case none were created above.
	// 	$comments[] = $this->generateComment($posts[0]->getId(), $user->getId());

	// 	$request = $this->createMock(Request::class);
	// 	$request->method('getRequestMethod')->willReturn('GET');
	// 	$request->method('getParameters')->willReturn([
	// 		'header' => [$user->getId(), 'comment'],
	// 		'body' => []
	// 	]);

	// 	$controller = new UserController($request);

	// 	$this->assertEquals('comment', $controller->getAction());

	// 	$response = $controller->doAction();

	// 	$this->assertInstanceOf(Response::class, $response);
	// 	$this->assertEquals('User comments were retrieved successfully!', $response->getMessage());
	// 	$this->assertEquals($user->getId(), $response->getPayload()['user']->getId());
	// 	$this->assertEquals(sizeOf($comments), sizeOf($response->getPayload()['comments']));

	// 	for ($i = 0; $i < sizeOf($comments); $i++) {
	// 		$this->assertEquals($comments[$i]->getContent(), $response->getPayload()['comments'][$i]->getContent());
	// 	}
	// }

	// public function testUserPostsWereRetrieved(): void
	// {
	// 	$user = $this->generateUser();

	// 	// User will randomly create on posts.
	// 	for ($i = 0; $i < rand(1, 20); $i++) {
	// 		if (rand(0, 1) === 0) {
	// 			$posts[] = $this->generatePost($user->getId());
	// 		} else {
	// 			$this->generatePost();
	// 		}
	// 	}

	// 	// Force a post in case none were created above.
	// 	$posts[] = $this->generatePost($user->getId());

	// 	$request = $this->createMock(Request::class);
	// 	$request->method('getRequestMethod')->willReturn('GET');
	// 	$request->method('getParameters')->willReturn([
	// 		'header' => [$user->getId(), 'post'],
	// 		'body' => []
	// 	]);

	// 	$controller = new UserController($request);

	// 	$this->assertEquals('post', $controller->getAction());

	// 	$response = $controller->doAction();

	// 	$this->assertInstanceOf(Response::class, $response);
	// 	$this->assertEquals('User posts were retrieved successfully!', $response->getMessage());
	// 	$this->assertEquals($user->getId(), $response->getPayload()['user']->getId());
	// 	$this->assertEquals(sizeOf($posts), sizeOf($response->getPayload()['posts']));

	// 	for ($i = 0; $i < sizeOf($posts); $i++) {
	// 		$this->assertEquals($posts[$i]->getContent(), $response->getPayload()['posts'][$i]->getContent());
	// 	}
	// }
}
