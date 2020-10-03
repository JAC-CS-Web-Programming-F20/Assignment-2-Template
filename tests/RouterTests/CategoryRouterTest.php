<?php

namespace AssignmentTwoTests\RouterTests;

use AssignmentTwoTests\AssignmentTwoTest;

final class CategoryRouterTest extends AssignmentTwoTest
{
	public function testCategoryWasCreatedSuccessfully(): void
	{
		$randomCategory = $this->generateCategoryData();

		$response = $this->getJsonResponse(
			'POST',
			'category',
			$randomCategory
		);

		$this->assertArrayHasKey('message', $response);
		$this->assertArrayHasKey('payload', $response);

		$payload = $response['payload'];

		$this->assertArrayHasKey('id', $payload);
		$this->assertArrayHasKey('title', $payload);
		$this->assertArrayHasKey('description', $payload);
		$this->assertArrayHasKey('createdBy', $payload);
		$this->assertEquals(1, $payload['id']);
		$this->assertEquals($randomCategory['title'], $payload['title']);
		$this->assertEquals($randomCategory['description'], $payload['description']);
		$this->assertIsArray($payload['createdBy']);

		$response = $this->getJsonResponse(
			'GET',
			'category/' . $payload['id']
		);

		$this->assertArrayHasKey('message', $response);
		$this->assertArrayHasKey('payload', $response);

		$payload = $response['payload'];

		$this->assertArrayHasKey('id', $payload);
		$this->assertArrayHasKey('title', $payload);
		$this->assertArrayHasKey('description', $payload);
		$this->assertArrayHasKey('createdBy', $payload);
		$this->assertEquals(1, $payload['id']);
		$this->assertEquals($randomCategory['title'], $payload['title']);
		$this->assertEquals($randomCategory['description'], $payload['description']);
		$this->assertIsArray($payload['createdBy']);
	}

	/**
	 * @dataProvider categoryBlankFieldsProvider
	 */
	public function testCategoryWasNotCreated(string $field): void
	{
		$randomCategory = $this->generateCategoryData();
		$randomCategory[$field] = '';

		$response = $this->getJsonResponse(
			'POST',
			'category',
			$randomCategory
		)['payload'];

		$this->assertEmpty($response);
	}

	public function categoryBlankFieldsProvider()
	{
		yield 'blank title' => ['title'];
		yield 'blank description' => ['description'];
	}

	public function testCategoryWasFoundById(): void
	{
		$randomCategory = $this->generateCategoryData();

		$newCategoryId = $this->getJsonResponse(
			'POST',
			'category',
			$randomCategory
		)['payload']['id'];

		$retrievedCategoryId = $this->getJsonResponse(
			'GET',
			'category/' . $newCategoryId
		)['payload']['id'];

		$this->assertEquals(
			$newCategoryId,
			$retrievedCategoryId
		);
	}

	public function testCategoryWasNotFoundByWrongId(): void
	{
		$randomCategory = $this->generateCategoryData();

		$newCategory = $this->getJsonResponse(
			'POST',
			'category',
			$randomCategory
		)['payload'];

		$retrievedCategory = $this->getJsonResponse(
			'GET',
			'category/' . $newCategory['id'] . '999',
		)['payload'];

		$this->assertEmpty($retrievedCategory);
	}

	/**
	 * @dataProvider updatedCategoryProvider
	 */
	public function testCategoryWasUpdated(array $oldCategoryData, array $newCategoryData, array $editedFields): void
	{
		$this->generateCategory();

		$oldCategory = $this->getJsonResponse(
			'POST',
			'category',
			$oldCategoryData
		)['payload'];

		$editedCategory = $this->getJsonResponse(
			'PUT',
			'category/' . $oldCategory['id'],
			$newCategoryData
		)['payload'];

		/**
		 * Check every Category field against all the fields that were supposed to be edited.
		 * If the Category field is a field that's supposed to be edited, check if they're not equal.
		 * If the Category field is not supposed to be edited, check if they're equal.
		 */
		foreach ($oldCategory as $oldCategoryKey => $oldCategoryValue) {
			foreach ($editedFields as $editedField) {
				if ($oldCategoryKey === $editedField) {
					$this->assertNotEquals($oldCategoryValue, $editedCategory[$editedField]);
					$this->assertEquals($editedCategory[$editedField], $newCategoryData[$editedField]);
				}
			}
		}
	}

	public function updatedCategoryProvider()
	{
		yield 'valid title' => [
			['title' => 'Pikachu', 'description' => 'pikachu@pokemon.com', 'createdBy' => 1],
			['title' => 'Bulbasaur'],
			['title'],
		];

		yield 'valid description' => [
			['title' => 'Pikachu', 'description' => 'pikachu@pokemon.com', 'createdBy' => 1],
			['description' => 'bulbasaur@pokemon.com'],
			['description'],
		];

		yield 'valid title and description' => [
			['title' => 'Pikachu', 'description' => 'pikachu@pokemon.com', 'createdBy' => 1],
			['title' => 'Magikarp', 'description' => 'magikarp@pokemon.com'],
			['title', 'description'],
		];
	}

	/**
	 * @dataProvider invalidUpdatedCategoryProvider
	 */
	public function testCategoryWasNotUpdated(array $oldCategoryData, array $newCategoryData): void
	{
		$this->generateCategory();

		$oldCategory = $this->getJsonResponse(
			'POST',
			'category',
			$oldCategoryData
		)['payload'];

		$editedCategory = $this->getJsonResponse(
			'PUT',
			'category/' . $oldCategory['id'],
			$newCategoryData
		)['payload'];

		$this->assertEmpty($editedCategory);
	}

	public function invalidUpdatedCategoryProvider()
	{
		yield 'blank Category name' => [
			['title' => 'Pikachu', 'description' => 'pikachu@pokemon.com', 'createdBy' => 1],
			['title' => ''],
		];

		yield 'integer Category name' => [
			['title' => 'Pikachu', 'description' => 'pikachu@pokemon.com', 'createdBy' => 1],
			['title' => 123],
		];

		yield 'blank Category type' => [
			['title' => 'Pikachu', 'description' => 'pikachu@pokemon.com', 'createdBy' => 1],
			['description' => ''],
		];

		yield 'integer Category type' => [
			['title' => 'Pikachu', 'description' => 'pikachu@pokemon.com', 'createdBy' => 1],
			['description' => 123],
		];
	}

	public function testCategoryWasDeletedSuccessfully(): void
	{
		$randomCategory = $this->generateCategoryData();

		$oldCategory = $this->getJsonResponse(
			'POST',
			'category',
			$randomCategory
		)['payload'];

		$this->assertEmpty($oldCategory['deletedAt']);

		$deletedCategory = $this->getJsonResponse(
			'DELETE',
			'category/' . $oldCategory['id']
		)['payload'];

		$this->assertEquals($oldCategory['id'], $deletedCategory['id']);
		$this->assertEquals($oldCategory['title'], $deletedCategory['title']);
		$this->assertEquals($oldCategory['description'], $deletedCategory['description']);

		$retrievedCategory = $this->getJsonResponse(
			'GET',
			'category/' . $oldCategory['id'],
		)['payload'];

		$this->assertNotEmpty($retrievedCategory['deletedAt']);
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
