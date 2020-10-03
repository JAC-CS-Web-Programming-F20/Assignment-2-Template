<?php

namespace AssignmentTwoTests\RouterTests;

use AssignmentTwoTests\AssignmentTwoTest;

final class CommentRouterTest extends AssignmentTwoTest
{
	public function testCommentWasCreatedSuccessfully(): void
	{
		$randomComment = $this->generateCommentData();

		$response = $this->getJsonResponse(
			'POST',
			'comment',
			$randomComment
		);

		$this->assertArrayHasKey('message', $response);
		$this->assertArrayHasKey('payload', $response);

		$payload = $response['payload'];

		$this->assertArrayHasKey('id', $payload);
		$this->assertArrayHasKey('user', $payload);
		$this->assertArrayHasKey('post', $payload);
		$this->assertArrayHasKey('replyTo', $payload);
		$this->assertArrayHasKey('content', $payload);
		$this->assertArrayHasKey('replies', $payload);
		$this->assertArrayHasKey('createdAt', $payload);
		$this->assertArrayHasKey('editedAt', $payload);
		$this->assertArrayHasKey('deletedAt', $payload);
		$this->assertEquals(1, $payload['id']);
		$this->assertEquals($randomComment['content'], $payload['content']);
	}

	/**
	 * @dataProvider commentBlankFieldsProvider
	 */
	public function testCommentWasNotCreated(string $field, $value): void
	{
		$randomComment = $this->generateCommentData();
		$randomComment[$field] = $value;

		$response = $this->getJsonResponse(
			'POST',
			'comment',
			$randomComment
		)['payload'];

		$this->assertEmpty($response);
	}

	public function commentBlankFieldsProvider()
	{
		yield 'blank userId' => ['userId', ''];
		yield 'string userId' => ['userId', 'abc'];
		yield 'blank postId' => ['postId', ''];
		yield 'string postId' => ['postId', 'abc'];
		yield 'blank content' => ['content', ''];
		yield 'string replyId' => ['replyId', 'abc'];
	}

	public function testCommentWasFoundById(): void
	{
		$randomComment = $this->generateCommentData();

		$newCommentId = $this->getJsonResponse(
			'POST',
			'comment',
			$randomComment
		)['payload']['id'];

		$retrievedCommentId = $this->getJsonResponse(
			'GET',
			'comment/' . $newCommentId
		)['payload']['id'];

		$this->assertEquals(
			$newCommentId,
			$retrievedCommentId
		);
	}

	public function testCommentWasNotFoundByWrongId(): void
	{
		$randomComment = $this->generateCommentData();

		$newComment = $this->getJsonResponse(
			'POST',
			'comment',
			$randomComment
		)['payload'];

		$retrievedComment = $this->getJsonResponse(
			'GET',
			'comment/' . $newComment['id'] . '999',
		)['payload'];

		$this->assertEmpty($retrievedComment);
	}

	/**
	 * @dataProvider updatedCommentProvider
	 */
	public function testCommentWasUpdated(array $oldCommentData, array $newCommentData, array $editedFields): void
	{
		$this->generateComment();

		$oldComment = $this->getJsonResponse(
			'POST',
			'comment',
			$oldCommentData
		)['payload'];

		$editedComment = $this->getJsonResponse(
			'PUT',
			'comment/' . $oldComment['id'],
			$newCommentData
		)['payload'];

		/**
		 * Check every Comment field against all the fields that were supposed to be edited.
		 * If the Comment field is a field that's supposed to be edited, check if they're not equal.
		 * If the Comment field is not supposed to be edited, check if they're equal.
		 */
		foreach ($oldComment as $oldCommentKey => $oldCommentValue) {
			foreach ($editedFields as $editedField) {
				if ($oldCommentKey === $editedField) {
					$this->assertNotEquals($oldCommentValue, $editedComment[$editedField]);
					$this->assertEquals($editedComment[$editedField], $newCommentData[$editedField]);
				}
			}
		}
	}

	public function updatedCommentProvider()
	{
		yield 'valid content' => [
			['postId' => 1, 'userId' => 1, 'content' => 'pikachu@pokemon.com', 'replyId' => null],
			['content' => 'Bulbasaur'],
			['content'],
		];
	}

	/**
	 * @dataProvider invalidUpdatedCommentProvider
	 */
	public function testCommentWasNotUpdated(array $oldCommentData, array $newCommentData): void
	{
		$this->generateComment();

		$oldComment = $this->getJsonResponse(
			'POST',
			'comment',
			$oldCommentData
		)['payload'];

		$editedComment = $this->getJsonResponse(
			'PUT',
			'comment/' . $oldComment['id'],
			$newCommentData
		)['payload'];

		$this->assertEmpty($editedComment);
	}

	public function invalidUpdatedCommentProvider()
	{
		yield 'blank text content' => [
			['postId' => 1, 'userId' => 1, 'content' => 'pikachu@pokemon.com', 'replyId' => null],
			['content' => ''],
		];
	}

	public function testCommentWasDeletedSuccessfully(): void
	{
		$randomComment = $this->generateCommentData();

		$oldComment = $this->getJsonResponse(
			'POST',
			'comment',
			$randomComment
		)['payload'];

		$this->assertEmpty($oldComment['deletedAt']);

		$deletedComment = $this->getJsonResponse(
			'DELETE',
			'comment/' . $oldComment['id']
		)['payload'];

		$this->assertEquals($oldComment['id'], $deletedComment['id']);
		$this->assertEquals($oldComment['content'], $deletedComment['content']);

		$retrievedComment = $this->getJsonResponse(
			'GET',
			'comment/' . $oldComment['id'],
		)['payload'];

		$this->assertNotEmpty($retrievedComment['deletedAt']);
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
