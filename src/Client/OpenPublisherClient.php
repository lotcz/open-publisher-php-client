<?php

declare(strict_types=1);

namespace Zavadil\OpenPublisher\Client;

use Zavadil\Common\Client\HttpClient;
use Zavadil\Common\Helpers\PathHelper;
use Zavadil\OpenPublisher\Client\Payload\ArticlesPage;
use Zavadil\OpenPublisher\Client\Payload\CategoriesPage;
use Zavadil\OpenPublisher\Client\Payload\CategoryStub;

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

	public function loadDestinationCategories(): CategoriesPage {
		return $this->get(
			"articles-sync/{$this->destinationName}/categories",
			null,
			CategoriesPage::class
		);
	}

	public function insertCategory(string $name): CategoryStub {
		return $this->post(
			"articles-sync/{$this->destinationName}/categories",
			$name,
			null,
			CategoryStub::class
		);
	}

	public function updateCategory(int $categoryId, string $name): CategoryStub {
		return $this->put(
			"articles-sync/{$this->destinationName}/categories/{$categoryId}",
			$name,
			null,
			CategoryStub::class
		);
	}

	public function deleteCategory(int $categoryId) {
		$this->delete("articles-sync/{$this->destinationName}/categories/{$categoryId}");
	}

	public function loadArticleCategories(int $articleId): array {
		return $this->get("articles-sync/{$this->destinationName}/articles/{$articleId}/categories");
	}

	public function getImageUrl(string $imageName): string {
		return PathHelper::of($this->baseUrl, "images", $imageName, "original");
	}

	public function downloadImage(string $imageName, string $savePath) {
		$url = $this->getImageUrl($imageName);

		// Create directory recursively if it doesn't exist
		$dir = dirname($savePath);
		if (!is_dir($dir)) {
			mkdir($dir, 0755, true); // true = recursive
		}

		$fp = fopen($savePath, 'wb');
		if (!$fp) {
			throw new \Exception("Couldn't open file {$savePath} for writing.");
			return;
		}

		$ch = curl_init($url);
		curl_setopt_array($ch, [
			CURLOPT_FILE => $fp,   // write directly to file
			CURLOPT_FOLLOWLOCATION => true,  // follow redirects
			CURLOPT_MAXREDIRS => 10,    // max redirect hops
			CURLOPT_TIMEOUT => 30,
			CURLOPT_USERAGENT => 'Mozilla/5.0',
		]);

		curl_exec($ch);
		$error = curl_error($ch);
		curl_close($ch);
		fclose($fp);

		if ($error) {
			unlink($savePath); // clean up partial file
			throw new \Exception($error);
		}
	}

}
