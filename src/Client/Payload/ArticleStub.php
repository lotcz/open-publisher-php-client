<?php

declare(strict_types=1);

namespace Zavadil\OpenPublisher\Client\Payload;

use DateTimeInterface;
use Zavadil\Common\Client\Payload\EntityBase;

class ArticleStub extends EntityBase {

	public int $ownerId;

	public ?int $partnerId;

	public int $destinationId;

	public string $articleState;

	public ?DateTimeInterface $publishDate;

	public ?string $imageName;

	public ?string $previewText;

	public ?string $contentHtml;

}
