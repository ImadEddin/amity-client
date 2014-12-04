<?php
/*
 * Copyright (c) 2014 - Dialogue1 GmbH - MIT licensed
 */

namespace dialogue1\amity\API;

use Guzzle\Http\Client as GuzzleHttpClient;
use Guzzle\Http\ClientInterface;
use Guzzle\Http\Exception as GuzzleEx;
use Guzzle\Http\Message\EntityEnclosingRequestInterface;
use Guzzle\Http\Message\RequestInterface;
use Symfony\Component\HttpKernel\Exception as SymfonyEx;

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
		$this->request('GET', '/');

		return true;
	}

	public function getContacts($page = 5000, $size = null, $email = null, $substring = null, $gender = null, $active = null) {
		$query = array();

		if ($page   !== null) $query['page']   = (int) $page;
		if ($size   !== null) $query['size']   = (int) $size;
		if ($active !== null) $query['active'] = $active ? 1 : 0;

		if ($gender !== null) {
			if (!in_array($gender, array('f', 'm', 'x'), true)) {
				throw new \InvalidArgumentException('$gender must be one of [f, m, x].');
			}

			$query['gender'] = $gender;
		}

		if ($email !== null) {
			$query['email'] = $email;

			if ($substring) {
				$query['substring'] = 1;
			}
		}

		$data = $this->request('GET', '/contacts', $query);

		return $data['data'];
	}

	public function getContact($id) {
		$data = $this->request('GET', '/contacts/'.$id);

		return $data['data'];
	}

	public function createContact(array $contactData, array $lists = array(), array $events = array()) {
		$data = $this->request('POST', '/contacts', array(), array(
			'contact' => $contactData,
			'lists'   => $lists,
			'events'  => $events
		));

		return $data['data'];
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

		try {
			$response = $request->send();
			$data     = @json_decode($response->getBody(true), true);

			if (!is_array($data)) {
				throw new SymfonyEx\ServiceUnavailableHttpException(null, 'API did not return JSON.');
			}

			return $data;
		}
		catch (GuzzleEx\BadResponseException $e) {
			throw $this->buildException($e);
		}
	}

	protected function buildException(GuzzleEx\BadResponseException $exception) {
		$response = $exception->getResponse();
		$body     = $response->getBody(true);
		$message  = $body;
		$code     = null;

		if ($response->isContentType('application/json')) {
			$data    = @json_decode($body, true);
			$message = isset($data['message']) ? $data['message'] : '(no error message given)';

			if (isset($data['status'])) {
				$status = (int) $data['status'];
				$code   = $status - ((int) ($status / 1000)) * 1000;
			}
		}

		switch ($response->getStatusCode()) {
			case 400: return new SymfonyEx\BadRequestHttpException($message, null, $code);
			case 401:
			case 403: return new SymfonyEx\AccessDeniedHttpException($message, null, $code);
			case 404: return new SymfonyEx\NotFoundHttpException($message, null, $code);
			case 405: return new SymfonyEx\MethodNotAllowedHttpException(array(), $message, null, $code);
			case 406: return new SymfonyEx\NotAcceptableHttpException($message, null, $code);
			case 409: return new SymfonyEx\ConflictHttpException($message, null, $code);
			case 415: return new SymfonyEx\UnsupportedMediaTypeHttpException($message, null, $code);
			case 500:
			case 503: return new SymfonyEx\ServiceUnavailableHttpException(null, $message, null, $code);
			default:  return new SymfonyEx\HttpException(500, $message, null, array(), $code);
		}
	}
}
