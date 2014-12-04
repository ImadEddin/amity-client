<?php
/*
 * Copyright (c) 2014 - Dialogue1 GmbH - MIT licensed
 */

namespace dialogue1\amity\API\Service;

use dialogue1\amity\API\Client;

class ContactListService {
	protected $apiClient;

	public function __construct(Client $apiClient) {
		$this->apiClient = $apiClient;
	}

	public function getAll($contactID) {
		return $this->apiClient->requestData('GET', '/contacts/'.$contactID.'/lists');
	}

	public function addContactToList($contactID, $listID) {
		return $this->apiClient->requestData('PUT', '/contacts/'.$contactID.'/lists/'.$listID);
	}

	public function removeContactFromList($contactID, $listID) {
		$this->apiClient->request('DELETE', '/contacts/'.$contactID.'/lists/'.$listID);
		return true;
	}
}
