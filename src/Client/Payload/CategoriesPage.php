<?php

namespace Zavadil\OpenPublisher\Client\Payload;

use Zavadil\Common\Client\Payload\PageBase;

class CategoriesPage extends PageBase {

	/**
	 * @var array<CategoryStub>
	 */
	public array $content = [];

	public function getContentClass(): string {
		return CategoryStub::class;
	}
}
