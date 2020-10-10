<?php

namespace AssignmentTwoTests;

use AssignmentTwo\Database\Connection;
use AssignmentTwo\Models\Category;
use AssignmentTwo\Models\Comment;
use AssignmentTwo\Models\Post;
use AssignmentTwo\Models\User;
use Faker\Factory;
use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

abstract class AssignmentTwoTest extends TestCase
{
	protected static $faker;
	protected static $client;

	public static function setUpBeforeClass(): void
	{
		self::$client = new Client([
			'base_uri' => 'http://apache/Assignments/assignment-2-githubusername/public/'
		]);

		self::$faker = Factory::create();
	}

	protected function generateUserData(): array
	{
		return [
			'username' => self::$faker->username,
			'email' => self::$faker->email,
			'password' => self::$faker->password
		];
	}

	protected function generateUser(): User
	{
		return User::create(
			self::$faker->username,
			self::$faker->email,
			self::$faker->password
		);
	}

	protected function generateCategory(User $user = null): Category
	{
		$user = $user ?? $this->generateUser();
		$categoryData = $this->generateCategoryData($user);

		return Category::create(
			$categoryData['createdBy'],
			$categoryData['title'],
			$categoryData['description']
		);
	}

	protected function generateCategoryData(User $user = null): array
	{
		$user = $user ?? $this->generateUser();

		return [
			'createdBy' => $user->getId(),
			'title' => self::$faker->word,
			'description' => self::$faker->sentence
		];
	}

	protected function generatePost(bool $forceTextPost = false, User $user = null, Category $category = null): Post
	{
		$postData = $this->generatePostData($forceTextPost, $user, $category);

		return Post::create(
			$postData['userId'],
			$postData['categoryId'],
			$postData['title'],
			$postData['type'],
			$postData['content']
		);
	}

	protected function generatePostData(bool $forceTextPost = false, User $user = null, Category $category = null): array
	{
		$postData['userId'] = empty($user) ? $this->generateUser()->getId() : $user->getId();
		$postData['categoryId'] = empty($category) ? $this->generateCategory()->getId() : $category->getId();
		$postData['title'] = self::$faker->word;

		if (rand(0, 1) === 0 || $forceTextPost) {
			$postData['type'] = 'Text';
			$postData['content'] = self::$faker->paragraph();
		} else {
			$postData['type'] = 'URL';
			$postData['content'] = self::$faker->url;
		}

		return $postData;
	}

	protected function generateComment(int $postId = null, int $userId = null, int $replyId = null): Comment
	{
		$comment = $this->generateCommentData($postId, $userId, $replyId);

		return Comment::create(
			$comment['postId'],
			$comment['userId'],
			$comment['content'],
			$comment['replyId']
		);
	}

	protected function generateCommentData(int $postId = null, int $userId = null, int $replyId = null): array
	{
		$postId = $postId ?? $this->generatePost()->getId();
		$userId = $userId ?? $this->generateUser()->getId();

		return [
			'postId' => $postId,
			'userId' => $userId,
			'content' => self::$faker->paragraph(),
			'replyId' => $replyId
		];
	}

	public function tearDown(): void
	{
		$database = new Connection();
		$connection = $database->connect();
		$statement = $connection->prepare("DELETE FROM `comment`");
		$statement->execute();
		$statement = $connection->prepare("ALTER TABLE `comment` AUTO_INCREMENT = 1");
		$statement->execute();
		$statement = $connection->prepare("DELETE FROM `post`");
		$statement->execute();
		$statement = $connection->prepare("ALTER TABLE `post` AUTO_INCREMENT = 1");
		$statement->execute();
		$statement = $connection->prepare("DELETE FROM `category`");
		$statement->execute();
		$statement = $connection->prepare("ALTER TABLE `category` AUTO_INCREMENT = 1");
		$statement->execute();
		$statement = $connection->prepare("DELETE FROM `user`");
		$statement->execute();
		$statement = $connection->prepare("ALTER TABLE `user` AUTO_INCREMENT = 1");
		$statement->execute();
		$statement->close();
	}
}
