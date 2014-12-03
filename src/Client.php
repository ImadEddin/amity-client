<?php
/*
 * Copyright (c) 2014 - Dialogue1 GmbH - MIT licensed
 */

namespace dialogue1\amity\API;

use Guzzle\Http\ClientInterface;
use Guzzle\Http\Message\RequestInterface;
use Guzzle\Http\Message\EntityEnclosingRequestInterface;

class Client {
	const API_VERSION = 'v2';

	protected $http;
	protected $clientID;
	protected $apiKey;

	public function __construct(ClientInterface $httpClient, $amityClientID, $amityApiKey) {
		$this->http     = $httpClient;
		$this->clientID = $amityClientID;
		$this->apiKey   = $amityApiKey;
	}

	public function checkConnection() {
		$response = $this->request('GET', '/');
	}

	protected function request($method, $uri, array $query = array(), array $data = array()) {
		$fullUri = '/api/'.self::API_VERSION.$uri;
		$request = $this->http->createRequest($method, $fullUri);

		$request->getQuery()->replace($query);

		$this->signRequest($request);

		return $request->send();
	}

	protected function signRequest(RequestInterface $request) {
		$query = $request->getQuery()->getAll();
		ksort($query);

		$query = http_build_query($query, '', '&');
		$query = str_replace(array(' ', '+'), '%20', $query);

		$payload = array(
			$request->getMethod(),
			$request->getUrl(true)->getPath(),
			$query
		);

		if ($request instanceof EntityEnclosingRequestInterface) {
			$body = $request->getBody();

			if ($body) {
				$payload[] = $body;
			}
		}

		$payload   = implode("\n", $payload);
		$signature = hash_hmac('sha256', $payload, $this->apiKey);

		$request->setHeader('X-Client', $this->clientID);
		$request->setHeader('X-Signature', $signature);
	}
}
