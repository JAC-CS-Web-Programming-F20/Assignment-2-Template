<?php

namespace AssignmentTwoTests\ModelTests;

use AssignmentTwo\Models\Post;
use AssignmentTwoTests\AssignmentTwoTest;

final class PostTest extends AssignmentTwoTest
{
	public function testPostWasCreatedSuccessfully(): void
	{
		$this->assertInstanceOf(Post::class, $this->generatePost());
	}

	public function testPostWasNotCreatedWithNonExistantUser(): void
	{
		$postData = $this->generatePostData();
		$post = Post::create(
			999,
			$postData['categoryId'],
			$postData['title'],
			$postData['type'],
			$postData['content']
		);

		$this->assertNull($post);
	}

	public function testPostWasNotCreatedWithNonExistantCategory(): void
	{
		$postData = $this->generatePostData();
		$post = Post::create(
			$postData['userId'],
			999,
			$postData['title'],
			$postData['type'],
			$postData['content']
		);

		$this->assertNull($post);
	}

	public function testPostWasNotCreatedWithBlankFields(): void
	{
		$user = $this->generateUser();
		$category = $this->generateCategory();
		$postData = $this->generatePostData();
		$postBlankTitle = Post::create(
			$user->getId(),
			$category->getId(),
			'',
			$postData['type'],
			$postData['content']
		);

		$this->assertNull($postBlankTitle);

		$postBlankType = Post::create(
			$user->getId(),
			$category->getId(),
			$postData['title'],
			'',
			$postData['content']
		);

		$this->assertNull($postBlankType);

		$postBlankContent = Post::create(
			$user->getId(),
			$category->getId(),
			$postData['title'],
			$postData['type'],
			''
		);

		$this->assertNull($postBlankContent);
	}

	public function testPostWasFoundById(): void
	{
		$newPost = $this->generatePost();
		$retreivedPost = Post::findById($newPost->getId());

		$this->assertEquals(
			$retreivedPost->getTitle(),
			$newPost->getTitle()
		);
	}

	public function testPostWasNotFoundByWrongId(): void
	{
		$newPost = $this->generatePost();
		$retreivedPost = Post::findById($newPost->getId() + 1);

		$this->assertNull($retreivedPost);
	}

	public function testTextPostContentsWasUpdatedSuccessfully(): void
	{
		$oldPost = $this->generatePost();
		$oldPost->setType('Text');
		$newPostContent = self::$faker->paragraph();

		$oldPost->setContent($newPostContent);
		$this->assertNull($oldPost->getEditedAt());
		$this->assertTrue($oldPost->save());

		$retreivedPost = Post::findById($oldPost->getId());
		$this->assertEquals($newPostContent, $retreivedPost->getContent());
		$this->assertNotNull($retreivedPost->getEditedAt());
	}

	public function testPostWasNotUpdatedWithBlankFields(): void
	{
		$post = $this->generatePost();
		$post->setContent('');
		$this->assertFalse($post->save());
	}

	public function testUrlPostWasNotUpdated(): void
	{
		$post = $this->generatePost();
		$post->setType('URL');
		$this->assertFalse($post->save());
	}

	public function testPostWasDeletedSuccessfully(): void
	{
		$post = $this->generatePost();

		$this->assertNull($post->getDeletedAt());
		$this->assertTrue($post->remove());

		$retreivedPost = Post::findById($post->getId());
		$this->assertNotNull($retreivedPost->getDeletedAt());
	}
}
