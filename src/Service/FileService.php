<?php
/*
 * Copyright (c) 2014 - Dialogue1 GmbH - MIT licensed
 */

namespace dialogue1\amity\API\Service;

use dialogue1\amity\API\Client;

class FileService {
	protected $apiClient;

	public function __construct(Client $apiClient) {
		$this->apiClient = $apiClient;
	}

	public function create($type, $filename, $sourceURL) {
		if (!in_array($type, array('img', 'data'), true)) {
			throw new \InvalidArgumentException('Bad file type (must be either "img" or "data").');
		}

		$uri = '/files/'.$type.'/'.$filename;

		return $this->apiClient->requestData('PUT', $uri, array(), array(
			'file' => array(
				'source' => $sourceURL
			)
		));
	}
}
