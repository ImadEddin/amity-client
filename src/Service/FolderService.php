<?php
/*
 * Copyright (c) 2014 - Dialogue1 GmbH - MIT licensed
 */

namespace dialogue1\amity\API\Service;

use dialogue1\amity\API\Client;

class FolderService {
	protected $apiClient;

	public function __construct(Client $apiClient) {
		$this->apiClient = $apiClient;
	}

	public function getAll($parentFolderID = null) {
		$uri = $parentFolderID === null ? '/folders' : '/folders/'.$parentFolderID.'/children';

		return $this->apiClient->requestData('GET', $uri);
	}

	public function getOne($folderID) {
		return $this->apiClient->requestData('GET', '/folders/'.$folderID);
	}

	public function create($label, $parentFolderID = null) {
		return $this->apiClient->requestData('POST', '/folders', array(), array(
			'folder' => array(
				'label'  => $label,
				'parent' => $parentFolderID
			)
		));
	}

	public function update($folderID, $label, $parentFolderID = null) {
		$payload = array('label' => $label);

		if ($parentFolderID !== null) {
			$payload['parent'] = $parentFolderID;
		}

		return $this->apiClient->requestData('PUT', '/folders/'.$folderID, array(), array(
			'folder' => $payload
		));
	}

	public function delete($folderID) {
		$this->apiClient->request('DELETE', '/folders/'.$folderID);
		return true;
	}
}
