<?php

declare(strict_types=1);

namespace Zavadil\OpenPublisher\Client;

use Zavadil\Common\Client\HttpClient;
use Zavadil\OpenPublisher\Client\Payload\ArticlesPage;

class OpenPublisherClient extends HttpClient {

	private AccessTokenManager $tokenManager;

	private string $destinationName;

	public function __construct(string $baseUrl, string $login, string $password, string $destinationName) {
		parent::__construct($baseUrl);
		$this->tokenManager = new AccessTokenManager($baseUrl, $login, $password);
		$this->destinationName = $destinationName;
	}

	protected function getHeaders(): array {
		$headers = parent::getHeaders();
		$headers['Authorization'] = 'Bearer ' . $this->tokenManager->getAccessTokenRaw();
		return $headers;
	}

	public function loadArticlesForImport(?int $size = 10): ArticlesPage {
		return $this->get(
			"articles-sync/{$this->destinationName}",
			['size' => $size],
			ArticlesPage::class
		);
	}

	public function updateLastSynced(\DateTime $date): void {
		$this->put("articles-sync/{$this->destinationName}/last-synced", $date);
	}

}
