<?php
/*
 * Copyright (c) 2014 - Dialogue1 GmbH - MIT licensed
 */

namespace dialogue1\amity\API\Service;

use dialogue1\amity\API\Client;

class BounceService {
	protected $apiClient;

	public function __construct(Client $apiClient) {
		$this->apiClient = $apiClient;
	}

	public function getMany($email = null, $substring = null, $mailingID = null, $month = null, $reaction = null) {
		$query = array();

		if ($mailingID !== null) {
			$query['mailing'] = $mailingID;
		}

		if ($email !== null) {
			$query['email'] = $email;

			if ($substring) {
				$query['substring'] = 1;
			}
		}

		if ($month !== null) {
			if (!preg_match('/^\d\d\d\d-\d\d$/', $month)) {
				throw new \InvalidArgumentException('Malformed month given (must be YYYY-MM).');
			}

			$query['month'] = $month;
		}

		if ($reaction !== null) {
			$allowed = array('recycled', 'excluded', 'recycledexcluded', 'anything');

			if (!in_array($reaction, $allowed, true)) {
				throw new \InvalidArgumentException('$reaction must be one of ['.implode(', ', $allowed).'].');
			}

			$query['reaction'] = $reaction;
		}

		return $this->apiClient->requestData('GET', '/bounces', $query);
	}
}
