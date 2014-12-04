<?php
/*
 * Copyright (c) 2014 - Dialogue1 GmbH - MIT licensed
 */

namespace dialogue1\amity\API\Service;

use dialogue1\amity\API\Client;

class AssetService {
	protected $apiClient;

	public function __construct(Client $apiClient) {
		$this->apiClient = $apiClient;
	}

	public function create($title, $name, $subject, $html, $linktracking = false, $multipart = false, $plain = null) {
		$payload = array(
			'title'        => $title,
			'name'         => $name,
			'subject'      => $subject,
			'html'         => $html,
			'linktracking' => !!$linktracking,
			'multipart'    => !!$multipart
		);

		if ($multipart) {
			$payload['plain'] = $plain;
		}

		return $this->apiClient->requestData('POST', '/assets', array(), array(
			'asset' => $payload
		));
	}
}
