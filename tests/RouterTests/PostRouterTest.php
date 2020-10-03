<?php

namespace AssignmentTwoTests\RouterTests;

use AssignmentTwoTests\AssignmentTwoTest;

final class PostRouterTest extends AssignmentTwoTest
{
	public function testPostWasCreatedSuccessfully(): void
	{
		$randomPost = $this->generatePostData();

		$response = $this->getJsonResponse(
			'POST',
			'post',
			$randomPost
		);

		$this->assertArrayHasKey('message', $response);
		$this->assertArrayHasKey('payload', $response);

		$payload = $response['payload'];

		$this->assertArrayHasKey('id', $payload);
		$this->assertArrayHasKey('user', $payload);
		$this->assertArrayHasKey('category', $payload);
		$this->assertArrayHasKey('title', $payload);
		$this->assertArrayHasKey('type', $payload);
		$this->assertArrayHasKey('content', $payload);
		$this->assertArrayHasKey('createdAt', $payload);
		$this->assertArrayHasKey('editedAt', $payload);
		$this->assertArrayHasKey('deletedAt', $payload);
		$this->assertEquals(1, $payload['id']);
		$this->assertEquals($randomPost['title'], $payload['title']);
		$this->assertEquals($randomPost['type'], $payload['type']);
		$this->assertEquals($randomPost['content'], $payload['content']);
	}

	/**
	 * @dataProvider postBlankFieldsProvider
	 */
	public function testPostWasNotCreated(string $field, $value): void
	{
		$randomPost = $this->generatePostData();
		$randomPost[$field] = $value;

		$response = $this->getJsonResponse(
			'POST',
			'post',
			$randomPost
		)['payload'];

		$this->assertEmpty($response);
	}

	public function postBlankFieldsProvider()
	{
		yield 'blank userId' => ['userId', ''];
		yield 'string userId' => ['userId', 'abc'];
		yield 'blank categoryId' => ['categoryId', ''];
		yield 'string categoryId' => ['categoryId', 'abc'];
		yield 'blank title' => ['title', ''];
		yield 'blank type' => ['type', ''];
		yield 'blank content' => ['content', ''];
	}

	public function testPostWasFoundById(): void
	{
		$randomPost = $this->generatePostData();

		$newPostId = $this->getJsonResponse(
			'POST',
			'post',
			$randomPost
		)['payload']['id'];

		$retrievedPostId = $this->getJsonResponse(
			'GET',
			'post/' . $newPostId
		)['payload']['id'];

		$this->assertEquals(
			$newPostId,
			$retrievedPostId
		);
	}

	public function testPostWasNotFoundByWrongId(): void
	{
		$randomPost = $this->generatePostData();

		$newPost = $this->getJsonResponse(
			'POST',
			'post',
			$randomPost
		)['payload'];

		$retrievedPost = $this->getJsonResponse(
			'GET',
			'post/' . $newPost['id'] . '999',
		)['payload'];

		$this->assertEmpty($retrievedPost);
	}

	/**
	 * @dataProvider updatedPostProvider
	 */
	public function testPostWasUpdated(array $oldPostData, array $newPostData, array $editedFields): void
	{
		$this->generatePost();

		$oldPost = $this->getJsonResponse(
			'POST',
			'post',
			$oldPostData
		)['payload'];

		$editedPost = $this->getJsonResponse(
			'PUT',
			'post/' . $oldPost['id'],
			$newPostData
		)['payload'];

		/**
		 * Check every Post field against all the fields that were supposed to be edited.
		 * If the Post field is a field that's supposed to be edited, check if they're not equal.
		 * If the Post field is not supposed to be edited, check if they're equal.
		 */
		foreach ($oldPost as $oldPostKey => $oldPostValue) {
			foreach ($editedFields as $editedField) {
				if ($oldPostKey === $editedField) {
					$this->assertNotEquals($oldPostValue, $editedPost[$editedField]);
					$this->assertEquals($editedPost[$editedField], $newPostData[$editedField]);
				}
			}
		}
	}

	public function updatedPostProvider()
	{
		yield 'valid content' => [
			['title' => 'Pikachu', 'type' => 'Text', 'content' => 'pikachu@pokemon.com', 'userId' => 1, 'categoryId' => 1],
			['content' => 'Bulbasaur'],
			['content'],
		];
	}

	/**
	 * @dataProvider invalidUpdatedPostProvider
	 */
	public function testPostWasNotUpdated(array $oldPostData, array $newPostData): void
	{
		$this->generatePost();

		$oldPost = $this->getJsonResponse(
			'POST',
			'post',
			$oldPostData
		)['payload'];

		$editedPost = $this->getJsonResponse(
			'PUT',
			'post/' . $oldPost['id'],
			$newPostData
		)['payload'];

		$this->assertEmpty($editedPost);
	}

	public function invalidUpdatedPostProvider()
	{
		yield 'blank text content' => [
			['title' => 'Pikachu', 'type' => 'Text', 'content' => 'pikachu@pokemon.com', 'userId' => 1, 'categoryId' => 1],
			['content' => ''],
		];

		yield 'new url' => [
			['title' => 'Pikachu', 'type' => 'URL', 'content' => 'pikachu@pokemon.com', 'userId' => 1, 'categoryId' => 1],
			['content' => 'bulbasaur@pokemon.com'],
		];
	}

	public function testPostWasDeletedSuccessfully(): void
	{
		$randomPost = $this->generatePostData();

		$oldPost = $this->getJsonResponse(
			'POST',
			'post',
			$randomPost
		)['payload'];

		$this->assertEmpty($oldPost['deletedAt']);

		$deletedPost = $this->getJsonResponse(
			'DELETE',
			'post/' . $oldPost['id']
		)['payload'];

		$this->assertEquals($oldPost['id'], $deletedPost['id']);
		$this->assertEquals($oldPost['title'], $deletedPost['title']);
		$this->assertEquals($oldPost['content'], $deletedPost['content']);

		$retrievedPost = $this->getJsonResponse(
			'GET',
			'post/' . $oldPost['id'],
		)['payload'];

		$this->assertNotEmpty($retrievedPost['deletedAt']);
	}

	private function getJsonResponse(string $method = 'GET', string $url = '', array $data = [])
	{
		$request = $this->buildRequest($method, $url, $data);
		$response = self::$client->request(
			$request['method'],
			$request['url'],
			$request['body']
		)->getBody();
		$jsonResponse = json_decode($response, true);
		return $jsonResponse;
	}

	private function buildRequest(string $method, string $url, array $data): array
	{
		$body['form_params'] = [];

		foreach ($data as $key => $value) {
			$body['form_params'][$key] = $value;
		}

		return [
			'method' => $method,
			'url' => $url,
			'body' => $body
		];
	}
}
