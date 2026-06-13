<?php

declare(strict_types=1);

namespace Zavadil\OpenPublisher\Client;

use Zavadil\Common\Client\HttpClient;
use Zavadil\Common\Client\OAuth\Payload\Request\RenewRefreshTokenPayload;
use Zavadil\Common\Client\OAuth\Payload\Request\RequestRefreshTokenFromLoginPayload;
use Zavadil\Common\Client\OAuth\Payload\Token\AccessTokenPayload;
use Zavadil\Common\Helpers\PathHelper;

class AccessTokensHttpClient extends HttpClient {

	public function __construct(string $baseUrl) {
		parent::__construct(PathHelper::of($baseUrl, "access-tokens"));
	}

	public function verifyAccessToken(string $accessToken): AccessTokenPayload {
		return $this->get("verify/{$accessToken}", null, AccessTokenPayload::class);
	}

	public function requestAccessTokenFromLogin(string $login, string $password): AccessTokenPayload {
		$request = new RequestRefreshTokenFromLoginPayload('OPEN-PUBLISHER', $login, $password);
		return $this->post('from-login', $request, null, AccessTokenPayload::class);
	}

	public function renewAccessToken(string $accessToken): AccessTokenPayload {
		$request = new RenewRefreshTokenPayload($accessToken);
		return $this->post('renew', $request, null, AccessTokenPayload::class);
	}

}
