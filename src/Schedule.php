<?php
/*
 * Copyright (c) 2014 - Dialogue1 GmbH - MIT licensed
 */

namespace dialogue1\amity\API;

class Schedule {
	protected $schedule;
	protected $date;
	protected $time;

	public function toArray(array $data = array()) {
		$data['schedule'] = $this->schedule;

		if ($this->date) {
			$data['date'] = $this->date;
		}

		if ($this->time) {
			$data['time'] = $this->time;
		}

		return $data;
	}

	public static function fromDateTime(\DateTime $dt) {
		$instance = new static();
		$instance->schedule = $dt->format(\DateTime::ISO8601);

		return $instance;
	}

	public static function now() {
		$instance = new static();
		$instance->schedule = 'now';

		return $instance;
	}

	public static function today($utcTime) {
		if (!preg_match('/^\d\d:\d\d(:\d\d)?$/', $utcTime)) {
			throw new \InvalidArgumentException('Malformed time given (must be HH:MM[:SS]).');
		}

		$instance = new static();
		$instance->schedule = 'today';
		$instance->time     = $utcTime;

		return $instance;
	}

	public static function later($date, $utcTime) {
		if (!preg_match('/^\d\d\d\d-\d\d-\d\d$/', $date)) {
			throw new \InvalidArgumentException('Malformed date given (must be YYYY-MM-DD).');
		}

		$instance = static::today($utcTime);
		$instance->schedule = 'later';
		$instance->date     = $date;

		return $instance;
	}
}
