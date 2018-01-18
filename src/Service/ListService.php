<?php
/*
 * Copyright (c) 2014 - Dialogue1 GmbH - MIT licensed
 */

namespace dialogue1\amity\API\Service;

use dialogue1\amity\API\Client;

class ListService {
	protected $apiClient;

	public function __construct(Client $apiClient) {
		$this->apiClient = $apiClient;
	}

	public function getAll() {
		return $this->apiClient->requestData('GET', '/lists');
	}

	public function getOne($listID) {
		return $this->apiClient->requestData('GET', '/lists/'.$listID);
	}

	public function create($label, $folderID = null, $test = false, $contactIDs = array()) {
		return $this->apiClient->requestData('POST', '/lists', array(), array(
			'list' => array(
				'label'  => $label,
				'folder' => $folderID,
				'test'   => !!$test
			),
			'contacts' => $contactIDs
		));
	}

	public function update($listID, $label, $folderID = null, $test = null) {
		$payload = array(
			'label'  => $label,
			'folder' => $folderID,
		);

		if ($test !== null) {
			$payload['test'] = !!$test;
		}

		return $this->apiClient->requestData('PUT', '/lists/'.$listID, array(), array(
			'list' => $payload
		));
	}

	public function delete($listID) {
		$this->apiClient->request('DELETE', '/lists/'.$listID);
		return true;
	}
}
