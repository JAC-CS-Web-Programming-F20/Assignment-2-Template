<?php

namespace AssignmentTwoTests\ModelTests;

use AssignmentTwo\Models\Comment;
use AssignmentTwoTests\AssignmentTwoTest;

final class CommentTest extends AssignmentTwoTest
{
	public function testCommentWasCreatedSuccessfully(): void
	{
		$this->assertInstanceOf(Comment::class, $this->generateComment());
	}

	public function testCommentWasNotCreatedWithNonExistantUser(): void
	{
		$comment = Comment::create(
			$this->generatePost()->getId(),
			999,
			self::$faker->paragraph()
		);

		$this->assertNull($comment);
	}

	public function testCommentWasNotCreatedWithNonExistantPost(): void
	{
		$comment = Comment::create(
			1,
			$this->generateUser()->getId(),
			self::$faker->paragraph()
		);

		$this->assertNull($comment);
	}

	public function testCommentWasNotCreatedWithBlankContent(): void
	{
		$commentBlankContent = Comment::create(
			$this->generatePost()->getId(),
			$this->generateUser()->getId(),
			''
		);

		$this->assertNull($commentBlankContent);
	}

	public function testCommentWasFoundById(): void
	{
		$newComment = $this->generateComment();
		$retreivedComment = Comment::findById($newComment->getId());

		$this->assertEquals(
			$retreivedComment->getContent(),
			$newComment->getContent()
		);
	}

	public function testCommentWasNotFoundByWrongId(): void
	{
		$newComment = $this->generateComment();
		$retreivedComment = Comment::findById($newComment->getId() + 1);

		$this->assertNull($retreivedComment);
	}

	public function testCommentContentsWasUpdatedSuccessfully(): void
	{
		$oldComment = $this->generateComment();
		$newCommentContent = self::$faker->paragraph();

		$oldComment->setContent($newCommentContent);
		$this->assertNull($oldComment->getEditedAt());
		$this->assertTrue($oldComment->save());

		$retreivedComment = Comment::findById($oldComment->getId());
		$this->assertEquals($newCommentContent, $retreivedComment->getContent());
		$this->assertNotNull($retreivedComment->getEditedAt());
	}

	public function testCommentWasNotUpdatedWithBlankFields(): void
	{
		$comment = $this->generateComment();
		$comment->setContent('');
		$this->assertFalse($comment->save());
	}

	public function testCommentWasDeletedSuccessfully(): void
	{
		$comment = $this->generateComment();

		$this->assertNull($comment->getDeletedAt());
		$this->assertTrue($comment->remove());

		$retreivedComment = Comment::findById($comment->getId());
		$this->assertNotNull($retreivedComment->getDeletedAt());
	}
}
