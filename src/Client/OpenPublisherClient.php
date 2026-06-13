<?php

declare(strict_types=1);

namespace Zavadil\OpenPublisher\Client;

use Zavadil\Common\Client\HttpClient;
use Zavadil\Common\Helpers\PathHelper;
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

	public function updateLastSynced(\DateTimeInterface $date): void {
		$this->put("articles-sync/{$this->destinationName}/last-synced", $date);
	}

	public function getImageUrl(string $imageName): string {
		return PathHelper::of($this->baseUrl, "images", $imageName, "original");
	}

	public function downloadImage(string $imageName, string $savePath): bool {
		$url = $this->getImageUrl($imageName);

		// Create directory recursively if it doesn't exist
		$dir = dirname($savePath);
		if (!is_dir($dir)) {
			mkdir($dir, 0755, true); // true = recursive
		}

		$fp = fopen($savePath, 'wb');
		if (!$fp) {
			return false;
		}

		$ch = curl_init($url);
		curl_setopt_array($ch, [
			CURLOPT_FILE           => $fp,   // write directly to file
			CURLOPT_FOLLOWLOCATION => true,  // follow redirects
			CURLOPT_MAXREDIRS      => 10,    // max redirect hops
			CURLOPT_TIMEOUT        => 30,
			CURLOPT_USERAGENT      => 'Mozilla/5.0',
		]);

		curl_exec($ch);
		$error = curl_error($ch);
		curl_close($ch);
		fclose($fp);

		if ($error) {
			unlink($savePath); // clean up partial file
			return false;
		}

		return true;
	}

}
