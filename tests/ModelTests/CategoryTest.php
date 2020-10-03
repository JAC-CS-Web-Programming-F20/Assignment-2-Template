<?php

namespace AssignmentTwoTests\ModelTests;

use AssignmentTwo\Models\Category;
use AssignmentTwoTests\AssignmentTwoTest;

final class CategoryTest extends AssignmentTwoTest
{
	public function testCategoryWasCreatedSuccessfully(): void
	{
		$this->assertInstanceOf(Category::class, $this->generateCategory());
	}

	public function testCategoryWasNotCreatedWithBlankTitle(): void
	{
		$user = $this->generateUser();
		$categoryBlankTitle = Category::create(
			$user->getId(),
			'',
			self::$faker->sentence
		);

		$this->assertNull($categoryBlankTitle);
	}

	public function testCategoryWasNotCreatedWithNonExistantUser(): void
	{
		$category = Category::create(
			1,
			self::$faker->word,
			self::$faker->sentence
		);

		$this->assertNull($category);
	}

	public function testCategoryWasNotCreatedWithDuplicateTitle(): void
	{
		$user = $this->generateUser();
		$title = self::$faker->word;
		$category1 = Category::create(
			$user->getId(),
			$title,
			self::$faker->sentence
		);

		$this->assertInstanceOf(Category::class, $category1);

		$category2 = Category::create(
			$user->getId(),
			$title,
			self::$faker->sentence
		);

		$this->assertNull($category2);
	}

	public function testCategoryWasFoundById(): void
	{
		$user = $this->generateUser();
		$newCategory = Category::create(
			$user->getId(),
			self::$faker->word,
			self::$faker->sentence
		);

		$retreivedCategory = Category::findById($newCategory->getId());

		$this->assertEquals(
			$retreivedCategory->getTitle(),
			$newCategory->getTitle()
		);
	}

	public function testCategoryWasNotFoundByWrongId(): void
	{
		$user = $this->generateUser();
		$newCategory = Category::create(
			$user->getId(),
			self::$faker->word,
			self::$faker->sentence
		);

		$retreivedCategory = Category::findById($newCategory->getId() + 1);

		$this->assertNull($retreivedCategory);
	}

	public function testCategoryWasFoundByTitle(): void
	{
		$user = $this->generateUser();
		$newCategory = Category::create(
			$user->getId(),
			self::$faker->word,
			self::$faker->sentence
		);

		$retreivedCategory = Category::findByTitle($newCategory->getTitle());

		$this->assertEquals(
			$retreivedCategory->getTitle(),
			$newCategory->getTitle()
		);
	}

	public function testCategoryWasNotFoundByWrongTitle(): void
	{
		$user = $this->generateUser();
		$title = self::$faker->word;

		Category::create(
			$user->getId(),
			$title,
			self::$faker->sentence
		);

		$retreivedCategory = Category::findByTitle($title . '.wrong');

		$this->assertNull($retreivedCategory);
	}

	public function testCategoryWasUpdatedSuccessfully(): void
	{
		$user = $this->generateUser();
		$oldCategory = Category::create(
			$user->getId(),
			self::$faker->word,
			self::$faker->sentence
		);

		$newCategoryTitle = self::$faker->word;

		$oldCategory->setTitle($newCategoryTitle);
		$this->assertNull($oldCategory->getEditedAt());
		$this->assertTrue($oldCategory->save());

		$retreivedCategory = Category::findById($oldCategory->getId());
		$this->assertEquals($newCategoryTitle, $retreivedCategory->getTitle());
		$this->assertNotNull($retreivedCategory->getEditedAt());
	}

	public function testCategoryWasNotUpdatedWithInvalidFields(): void
	{
		$user1 = $this->generateUser();
		$category1 = Category::create(
			$user1->getId(),
			self::$faker->word,
			self::$faker->sentence
		);

		$category1->setTitle('');
		$this->assertFalse($category1->save());

		$user2 = $this->generateUser();
		$category2 = Category::create(
			$user2->getId(),
			self::$faker->word,
			self::$faker->sentence
		);

		$category2->setDescription('');
		$this->assertFalse($category2->save());
	}

	public function testCategoryWasDeletedSuccessfully(): void
	{
		$user = $this->generateUser();
		$category = Category::create(
			$user->getId(),
			self::$faker->word,
			self::$faker->sentence
		);

		$this->assertNull($category->getDeletedAt());
		$this->assertTrue($category->remove());

		$retreivedCategory = Category::findById($category->getId());
		$this->assertNotNull($retreivedCategory->getDeletedAt());
	}
}
