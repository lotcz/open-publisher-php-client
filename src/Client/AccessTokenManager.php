<?php

declare(strict_types=1);

namespace Zavadil\OpenPublisher\Client;

use Zavadil\Common\Client\OAuth\Payload\Token\AccessTokenPayload;
use Zavadil\Common\Helpers\OAuthHelper;

class AccessTokenManager {

	private string $login;

	private string $password;

	private AccessTokensHttpClient $accessTokensClient;

	private ?AccessTokenPayload $accessToken = null;

	public function __construct(
		string $baseUrl,
		string $login,
		string $password
	) {
		$this->login = $login;
		$this->password = $password;
		$this->accessTokensClient = new AccessTokensHttpClient($baseUrl);
	}

	private function hasValidAccessToken(): bool {
		return OAuthHelper::isValidToken($this->accesssToken);
	}

	/**
	 * Get refresh token, renew it if needed
	 */
	public function getAccessToken(): AccessTokenPayload {
		if (!$this->hasValidAccessToken()) {
			$this->accessToken = $this->accessTokensClient->requestAccessTokenFromLogin(
				$this->login,
				$this->password
			);
		}
		if (OAuthHelper::isTokenReadyForRefresh($this->accessToken)) {
			$this->accessToken = $this->accessTokensClient->renewAccessToken($this->accessToken->token);
		}
		if (!$this->hasValidAccessToken()) {
			throw new \Exception("Failed to obtain valid access token");
		}
		return $this->accessToken;
	}

	public function getAccessTokenRaw(): string {
		$accessToken = $this->getAccessToken();
		return $accessToken->token;
	}

}
