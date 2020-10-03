<?php

namespace AssignmentTwoTests\RouterTests;

use AssignmentTwoTests\AssignmentTwoTest;

final class UserRouterTest extends AssignmentTwoTest
{
	public function testHome(): void
	{
		$response = $this->getJsonResponse(
			'GET',
			''
		);

		$this->assertArrayHasKey('message', $response);
		$this->assertArrayHasKey('payload', $response);
		$this->assertEquals('Homepage!', $response['message']);
	}

	public function testInvalidEndpoint(): void
	{
		$response = $this->getJsonResponse(
			'GET',
			'digimon'
		);

		$this->assertArrayHasKey('message', $response);
		$this->assertArrayHasKey('payload', $response);
		$this->assertEquals('404', $response['message']);
	}

	public function testInvalidHttpMethod(): void
	{
		$response = $this->getJsonResponse(
			'PATCH',
			'user'
		);

		$this->assertArrayHasKey('message', $response);
		$this->assertArrayHasKey('payload', $response);
		$this->assertEquals('404', $response['message']);
	}

	public function testUserWasCreatedSuccessfully(): void
	{
		$randomUser = $this->generateUserData();

		$response = $this->getJsonResponse(
			'POST',
			'user',
			$randomUser
		);

		$this->assertArrayHasKey('message', $response);
		$this->assertArrayHasKey('payload', $response);

		$payload = $response['payload'];

		$this->assertArrayHasKey('id', $payload);
		$this->assertArrayHasKey('username', $payload);
		$this->assertArrayHasKey('email', $payload);
		$this->assertEquals(1, $payload['id']);
		$this->assertEquals($randomUser['username'], $payload['username']);
		$this->assertEquals($randomUser['email'], $payload['email']);
	}

	/**
	 * @dataProvider userBlankFieldsProvider
	 */
	public function testUserWasNotCreated(string $field): void
	{
		$randomUser = $this->generateUserData();
		$randomUser[$field] = '';

		$response = $this->getJsonResponse(
			'POST',
			'user',
			$randomUser
		)['payload'];

		$this->assertEmpty($response);
	}

	public function userBlankFieldsProvider()
	{
		yield 'blank username' => ['username'];
		yield 'blank email' => ['email'];
		yield 'blank password' => ['password'];
	}

	public function testUserWasNotCreatedWithDuplicateName(): void
	{
		$randomUser = $this->generateUserData();

		$response = $this->getJsonResponse(
			'POST',
			'user',
			$randomUser
		)['payload'];

		$response = $this->getJsonResponse(
			'POST',
			'user',
			$randomUser
		)['payload'];

		$this->assertEmpty($response);
	}

	public function testUserWasFoundById(): void
	{
		$randomUserData = $this->generateUserData();

		$newUser = $this->getJsonResponse(
			'POST',
			'user',
			$randomUserData
		)['payload'];

		$retrievedUser = $this->getJsonResponse(
			'GET',
			'user/' . $newUser['id']
		)['payload'];

		$this->assertArrayHasKey('id', $retrievedUser);
		$this->assertArrayHasKey('username', $retrievedUser);
		$this->assertArrayHasKey('email', $retrievedUser);
		$this->assertEquals(1, $retrievedUser['id']);
		$this->assertEquals($newUser['username'], $retrievedUser['username']);
		$this->assertEquals($newUser['email'], $retrievedUser['email']);
	}

	public function testUserWasNotFoundByWrongId(): void
	{
		$randomUser = $this->generateUserData();

		$newUser = $this->getJsonResponse(
			'POST',
			'user',
			$randomUser
		)['payload'];

		$retrievedUser = $this->getJsonResponse(
			'GET',
			'user/' . $newUser['id'] . '923',
		)['payload'];

		$this->assertEmpty($retrievedUser);
	}

	// // public function testUserWasFoundByName(): void
	// // {
	// // 	$randomUser = $this->generateRandomUserData();

	// // 	$newUser = User::create(
	// // 		self::$database,
	// // 		$randomUser['username'],
	// // 		$randomUser['email'],
	// // 	);

	// // 	$retreivedUser = User::findByName(
	// // 		self::$database,
	// // 		$newUser->getName()
	// // 	);

	// // 	$this->assertEquals(
	// // 		$retreivedUser->getName(),
	// // 		$newUser->getName()
	// // 	);
	// // }

	// // public function testUserWasNotFoundByWrongName(): void
	// // {
	// // 	$randomUser = $this->generateRandomUserData();

	// // 	User::create(
	// // 		self::$database,
	// // 		$randomUser['username'],
	// // 		$randomUser['email'],
	// // 	);

	// // 	$retreivedUser = User::findByName(
	// // 		self::$database,
	// // 		$randomUser['username'] . '!'
	// // 	);

	// // 	$this->assertNull($retreivedUser);
	// // }

	/**
	 * @dataProvider updatedUserProvider
	 */
	public function testUserWasUpdated(array $oldUserData, array $newUserData, array $editedFields): void
	{
		$oldUser = $this->getJsonResponse(
			'POST',
			'user',
			$oldUserData
		)['payload'];

		$editedUser = $this->getJsonResponse(
			'PUT',
			'user/' . $oldUser['id'],
			$newUserData
		)['payload'];

		/**
		 * Check every User field against all the fields that were supposed to be edited.
		 * If the User field is a field that's supposed to be edited, check if they're not equal.
		 * If the User field is not supposed to be edited, check if they're equal.
		 */
		foreach ($oldUser as $oldUserKey => $oldUserValue) {
			foreach ($editedFields as $editedField) {
				if ($oldUserKey === $editedField) {
					$this->assertNotEquals($oldUserValue, $editedUser[$editedField]);
					$this->assertEquals($editedUser[$editedField], $newUserData[$editedField]);
				}
			}
		}
	}

	public function updatedUserProvider()
	{
		yield 'valid username' => [
			['username' => 'Pikachu', 'email' => 'pikachu@pokemon.com', 'password' => 'pikachu123'],
			['username' => 'Bulbasaur'],
			['username'],
		];

		yield 'valid email' => [
			['username' => 'Pikachu', 'email' => 'pikachu@pokemon.com', 'password' => 'pikachu123'],
			['email' => 'bulbasaur@pokemon.com'],
			['email'],
		];

		yield 'valid username and email' => [
			['username' => 'Pikachu', 'email' => 'pikachu@pokemon.com', 'password' => 'pikachu123'],
			['username' => 'Magikarp', 'email' => 'magikarp@pokemon.com'],
			['username', 'email'],
		];
	}

	/**
	 * @dataProvider invalidUpdatedUserProvider
	 */
	public function testUserWasNotUpdated(array $oldUserData, array $newUserData): void
	{
		$oldUser = $this->getJsonResponse(
			'POST',
			'user',
			$oldUserData
		)['payload'];

		$editedUser = $this->getJsonResponse(
			'PUT',
			'user/' . $oldUser['id'],
			$newUserData
		)['payload'];

		$this->assertEmpty($editedUser);
	}

	public function invalidUpdatedUserProvider()
	{
		yield 'blank User name' => [
			['username' => 'Pikachu', 'email' => 'pikachu@pokemon.com', 'password' => 'pikachu123'],
			['username' => ''],
		];

		yield 'integer User name' => [
			['username' => 'Pikachu', 'email' => 'pikachu@pokemon.com', 'password' => 'pikachu123'],
			['username' => 123],
		];

		yield 'blank User type' => [
			['username' => 'Pikachu', 'email' => 'pikachu@pokemon.com', 'password' => 'pikachu123'],
			['email' => ''],
		];

		yield 'integer User type' => [
			['username' => 'Pikachu', 'email' => 'pikachu@pokemon.com', 'password' => 'pikachu123'],
			['email' => 123],
		];
	}

	public function testUserWasDeletedSuccessfully(): void
	{
		$randomUser = $this->generateUserData();

		$oldUser = $this->getJsonResponse(
			'POST',
			'user',
			$randomUser
		)['payload'];

		$this->assertEmpty($oldUser['deletedAt']);

		$deletedUser = $this->getJsonResponse(
			'DELETE',
			'user/' . $oldUser['id']
		)['payload'];

		$this->assertEquals($oldUser['id'], $deletedUser['id']);
		$this->assertEquals($oldUser['username'], $deletedUser['username']);
		$this->assertEquals($oldUser['email'], $deletedUser['email']);

		$retrievedUser = $this->getJsonResponse(
			'GET',
			'user/' . $oldUser['id'],
		)['payload'];

		$this->assertNotEmpty($retrievedUser['deletedAt']);
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
