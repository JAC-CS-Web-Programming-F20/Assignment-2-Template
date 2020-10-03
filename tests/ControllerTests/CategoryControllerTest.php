<?php

namespace AssignmentTwoTests\ControllerTests;

use AssignmentTwo\Controllers\CategoryController;
use AssignmentTwo\Router\Request;
use AssignmentTwo\Router\Response;
use AssignmentTwoTests\AssignmentTwoTest;

final class CategoryControllerTest extends AssignmentTwoTest
{
	public function testCategoryControllerCalledShow(): void
	{
		$category = $this->generateCategory();

		$request = $this->createMock(Request::class);
		$request->method('getRequestMethod')->willReturn('GET');
		$request->method('getParameters')->willReturn([
			'body' => [],
			'header' => [$category->getId()]
		]);

		$controller = new CategoryController($request);

		$this->assertEquals('show', $controller->getAction());

		$response = $controller->doAction();

		$this->assertInstanceOf(Response::class, $response);
		$this->assertEquals('Category was retrieved successfully!', $response->getMessage());
		$this->assertEquals($category->getId(), $response->getPayload()->getId());
		$this->assertEquals($category->getTitle(), $response->getPayload()->getTitle());
		$this->assertEquals($category->getDescription(), $response->getPayload()->getDescription());
	}

	public function testCategoryControllerCalledNew(): void
	{
		$category = $this->generateCategoryData();

		$request = $this->createMock(Request::class);
		$request->method('getRequestMethod')->willReturn('POST');
		$request->method('getParameters')->willReturn([
			'body' => [
				'createdBy' => $category['createdBy'],
				'title' => $category['title'],
				'description' => $category['description']
			],
			'header' => []
		]);

		$controller = new CategoryController($request);

		$this->assertEquals('new', $controller->getAction());

		$response = $controller->doAction();

		$this->assertInstanceOf(Response::class, $response);
		$this->assertEquals('Category was created successfully!', $response->getMessage());
		$this->assertEquals($category['title'], $response->getPayload()->getTitle());
		$this->assertEquals($category['description'], $response->getPayload()->getDescription());
		$this->assertNotEmpty($response->getPayload()->getId());
	}

	public function testCategoryControllerCalledEdit(): void
	{
		$category = $this->generateCategory();
		$newCategoryData = $this->generateCategoryData();

		$request = $this->createMock(Request::class);
		$request->method('getRequestMethod')->willReturn('PUT');
		$request->method('getParameters')->willReturn([
			'body' => [
				'title' => $newCategoryData['title'],
				'description' => $newCategoryData['description'],
			],
			'header' => [$category->getId()]
		]);

		$controller = new CategoryController($request);

		$this->assertEquals('edit', $controller->getAction());

		$response = $controller->doAction();

		$this->assertInstanceOf(Response::class, $response);
		$this->assertEquals('Category was updated successfully!', $response->getMessage());
		$this->assertEquals($newCategoryData['title'], $response->getPayload()->getTitle());
		$this->assertEquals($newCategoryData['description'], $response->getPayload()->getDescription());
		$this->assertNotEquals($category->getTitle(), $response->getPayload()->getDescription());
		$this->assertNotEquals($category->getDescription(), $response->getPayload()->getTitle());
	}

	public function testCategoryControllerCalledDestroy(): void
	{
		$category = $this->generateCategory();

		$request = $this->createMock(Request::class);
		$request->method('getRequestMethod')->willReturn('DELETE');
		$request->method('getParameters')->willReturn([
			'body' => [],
			'header' => [$category->getId()]
		]);

		$controller = new CategoryController($request);

		$this->assertEquals('destroy', $controller->getAction());

		$response = $controller->doAction();

		$this->assertInstanceOf(Response::class, $response);
		$this->assertEquals('Category was deleted successfully!', $response->getMessage());
		$this->assertEquals($category->getId(), $response->getPayload()->getId());
		$this->assertEquals($category->getTitle(), $response->getPayload()->getTitle());
		$this->assertEquals($category->getDescription(), $response->getPayload()->getDescription());
	}
}
