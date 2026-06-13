<?php

namespace Zavadil\OpenPublisher\Client\Payload;

use Zavadil\Common\Client\Payload\PageBase;

class ArticlesPage extends PageBase {

	/**
	 * @var array<ArticleStub>
	 */
	public array $content = [];

	public function getContentClass(): string {
		return ArticleStub::class;
	}
}
