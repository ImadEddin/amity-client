<?php
/*
 * Copyright (c) 2014 - Dialogue1 GmbH - MIT licensed
 */

namespace dialogue1\amity\API\Service;

use dialogue1\amity\API\Client;
use dialogue1\amity\API\Schedule;

class MailingService {
	protected $apiClient;

	public function __construct(Client $apiClient) {
		$this->apiClient = $apiClient;
	}

	public function create($label, $assetID, $categoryID, Schedule $schedule, $approved = false) {
		$payload = array(
			'label'    => $label,
			'asset'    => $assetID,
			'category' => $categoryID,
			'approved' => !!$approved,
		);

		$payload = $schedule->toArray($payload);

		return $this->apiClient->requestData('POST', '/mailings', array(), array(
			'mailing' => $payload
		));
	}
}
