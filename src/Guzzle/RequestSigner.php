<?php
/*
 * Copyright (c) 2014 - Dialogue1 GmbH - MIT licensed
 */

namespace dialogue1\amity\API\Guzzle;

use Guzzle\Common\Event;
use Guzzle\Http\Message\EntityEnclosingRequestInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class RequestSigner implements EventSubscriberInterface {
	protected $clientID;
	protected $apiKey;

	public function __construct($amityClientID, $amityApiKey) {
		$this->clientID = $amityClientID;
		$this->apiKey   = $amityApiKey;
	}

	public static function getSubscribedEvents() {
		return array('request.before_send' => array('onBeforeSendRequest'));
	}

	public function onBeforeSendRequest(Event $event) {
		$request = $event['request'];

		// prepare query string

		$query = $request->getQuery()->getAll();
		ksort($query);

		$query = http_build_query($query, '', '&');
		$query = str_replace(array(' ', '+'), '%20', $query);

		// prepare payload

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

		// calculate and add signature

		$payload   = implode("\n", $payload);
		$signature = hash_hmac('sha256', $payload, $this->apiKey);

		$request->setHeader('X-Client', $this->clientID);
		$request->setHeader('X-Signature', $signature);
	}
}
