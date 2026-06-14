<?php

declare(strict_types=1);

namespace Zavadil\OpenPublisher\Client\Payload;

use DateTimeInterface;
use Zavadil\Common\Client\Payload\EntityBase;
use Zavadil\Common\Client\Payload\EntityWithName;

class CategoryStub extends EntityWithName {

	public int $destinationId;

}
