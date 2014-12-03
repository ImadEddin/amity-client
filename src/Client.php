<?php
/*
 * Copyright (c) 2014 - Dialogue1 GmbH - MIT licensed
 */

namespace dialogue1\amity\API;

use dialogue1\amity\API\Guzzle\RequestSigner;
use Guzzle\Http\Client as GuzzleHttpClient;
use Guzzle\Http\ClientInterface;
use Guzzle\Http\Message\EntityEnclosingRequestInterface;
use Guzzle\Http\Message\RequestInterface;

class Client {
	const API_VERSION = 'v2';

	protected $http;

	public function __construct(ClientInterface $httpClient) {
		$this->http = $httpClient;
	}

	public static function create($host, $clientID, $apiKey) {
		$guzzle = new GuzzleHttpClient('http://'.$host.'/');
		$signer = new RequestSigner($clientID, $apiKey);

		$guzzle->addSubscriber($signer);

		return new static($guzzle);
	}

	public function checkConnection() {
		$response = $this->request('GET', '/');
	}

	protected function request($method, $uri, array $query = array(), array $body = array()) {
		$fullUri = '/api/'.self::API_VERSION.$uri;
		$body    = json_encode($body);
		$headers = array(
			'Content-Type' => 'application/json; charset=UTF-8',
			'Accept'       => 'application/json',
			'User-Agent'   => 'dialogue1/amity-client 1.0'
		);

		$request = $this->http->createRequest($method, $fullUri, $headers, $body);
		$request->getQuery()->replace($query);

		return $request->send();
	}
}
