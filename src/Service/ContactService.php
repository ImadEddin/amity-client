<?php
/*
 * Copyright (c) 2014 - Dialogue1 GmbH - MIT licensed
 */

namespace dialogue1\amity\API\Service;

use dialogue1\amity\API\Client;

class ContactService {
	protected $apiClient;

	public function __construct(Client $apiClient) {
		$this->apiClient = $apiClient;
	}

	public function getMany($page = null, $size = null, $email = null, $substring = null, $gender = null, $active = null) {
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

		return $this->apiClient->requestData('GET', '/contacts', $query);
	}

	public function getOne($contactID) {
		return $this->apiClient->requestData('GET', '/contacts/'.$contactID);
	}

	public function create(array $contactData, array $lists = array(), array $events = array()) {
		return $this->apiClient->requestData('POST', '/contacts', array(), array(
			'contact' => $contactData,
			'lists'   => $lists,
			'events'  => $events
		));
	}

	public function update($contactID, array $contactData, array $events = array()) {
		return $this->apiClient->requestData('PUT', '/contacts/'.$contactID, array(), array(
			'contact' => $contactData,
			'events'  => $events
		));
	}

	public function trash($contactID) {
		return $this->update($contactID, array('recyclebin' => true));
	}

	public function untrash($contactID) {
		return $this->update($contactID, array('recyclebin' => false));
	}

	public function delete($contactID) {
		$this->apiClient->request('DELETE', '/contacts/'.$contactID);
		return true;
	}
}
