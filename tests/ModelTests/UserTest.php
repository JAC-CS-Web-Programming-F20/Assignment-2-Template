<?php

namespace AssignmentTwoTests\ModelTests;

use AssignmentTwo\Models\User;
use AssignmentTwoTests\AssignmentTwoTest;

final class UserTest extends AssignmentTwoTest
{
	public function testUserWasCreatedSuccessfully(): void
	{
		$this->assertInstanceOf(User::class, $this->generateUser());
	}

	public function testUserWasNotCreatedWithBlankFields(): void
	{
		$userBlankUsername = User::create(
			'',
			self::$faker->email,
			self::$faker->password
		);

		$this->assertNull($userBlankUsername);

		$userBlankEmail = User::create(
			self::$faker->username,
			'',
			self::$faker->password
		);

		$this->assertNull($userBlankEmail);

		$userBlankPassword = User::create(
			self::$faker->username,
			self::$faker->email,
			''
		);

		$this->assertNull($userBlankPassword);
	}

	public function testUserWasNotCreatedWithDuplicateEmail(): void
	{
		$email = self::$faker->email;

		User::create(
			self::$faker->username,
			$email,
			self::$faker->password
		);

		$user = User::create(
			self::$faker->username,
			$email,
			self::$faker->password
		);

		$this->assertNull($user);
	}

	public function testUserWasFoundById(): void
	{
		$username = self::$faker->username;
		$newUser = User::create(
			$username,
			self::$faker->email,
			self::$faker->password
		);

		$retrievedUser = User::findById($newUser->getId());

		$this->assertEquals(
			$retrievedUser->getUsername(),
			$newUser->getUsername()
		);
	}

	public function testUserWasNotFoundByWrongId(): void
	{
		$newUser = User::create(
			self::$faker->username,
			self::$faker->email,
			self::$faker->password
		);

		$retrievedUser = User::findById($newUser->getId() + 1);

		$this->assertNull($retrievedUser);
	}

	public function testUserWasFoundByEmail(): void
	{
		$email = self::$faker->email;
		$newUser = User::create(
			self::$faker->username,
			$email,
			self::$faker->password
		);

		$retrievedUser = User::findByEmail($newUser->getEmail());

		$this->assertEquals(
			$retrievedUser->getEmail(),
			$newUser->getEmail()
		);
	}

	public function testUserWasNotFoundByWrongEmail(): void
	{
		$email = self::$faker->email;
		User::create(
			self::$faker->username,
			$email,
			self::$faker->password
		);

		$retrievedUser = User::findByEmail($email . '.wrong');

		$this->assertNull($retrievedUser);
	}

	public function testUserWasUpdatedSuccessfully(): void
	{
		$oldUser = User::create(
			self::$faker->username,
			self::$faker->email,
			self::$faker->password
		);

		$newUsername = self::$faker->name;

		$oldUser->setUsername($newUsername);
		$this->assertNull($oldUser->getEditedAt());
		$this->assertTrue($oldUser->save());

		$retrievedUser = User::findById($oldUser->getId());
		$this->assertEquals($newUsername, $retrievedUser->getUsername());
		$this->assertNotNull($retrievedUser->getEditedAt());
	}

	public function testUserWasNotUpdatedWithInvalidFields(): void
	{
		$user1 = User::create(
			self::$faker->username,
			self::$faker->email,
			self::$faker->password
		);

		$user1->setUsername('');
		$this->assertFalse($user1->save());

		$user2 = User::create(
			self::$faker->username,
			self::$faker->email,
			self::$faker->password
		);

		$user2->setEmail('');
		$this->assertFalse($user2->save());

		$user3 = User::create(
			self::$faker->username,
			self::$faker->email,
			self::$faker->password
		);

		$user3->setPostScore(-1);
		$this->assertFalse($user3->save());

		$user4 = User::create(
			self::$faker->username,
			self::$faker->email,
			self::$faker->password
		);

		$user4->setCommentScore(-1);
		$this->assertFalse($user4->save());
	}

	public function testUserWasDeletedSuccessfully(): void
	{
		$user = User::create(
			self::$faker->username,
			self::$faker->email,
			self::$faker->password
		);
		$this->assertNull($user->getDeletedAt());
		$this->assertTrue($user->remove());

		$retrievedUser = User::findById($user->getId());
		$this->assertNotNull($retrievedUser->getDeletedAt());
	}
}
