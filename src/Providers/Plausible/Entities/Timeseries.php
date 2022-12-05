<?php

namespace Innocode\Statistics\Providers\Plausible\Entities;

use Innocode\Statistics\Abstracts\AbstractEntity;
use DateTime;

class Timeseries extends AbstractEntity {

	/**
	 * @var DateTime
	 */
	protected $date;
	/**
	 * @var int
	 */
	protected $visitors;
	/**
	 * @var int
	 */
	protected $pageviews;
	/**
	 * @var int
	 */
	protected $bounce_rate;
	/**
	 * @var int
	 */
	protected $visit_duration;
	/**
	 * @var int
	 */
	protected $visits;

	/**
	 * @param int $visitors
	 *
	 * @return void
	 */
	public function set_visitors( int $visitors ): void {
		$this->visitors = $visitors;
	}

	/**
	 * @return int
	 */
	public function get_visitors(): int {
		return $this->visitors;
	}

	/**
	 * @param int $pageviews
	 *
	 * @return void
	 */
	public function set_pageviews( int $pageviews ): void {
		$this->pageviews = $pageviews;
	}

	/**
	 * @return int
	 */
	public function get_pageviews(): int {
		return $this->pageviews;
	}

	/**
	 * @param int $bounce_rate
	 *
	 * @return void
	 */
	public function set_bounce_rate( int $bounce_rate ): void {
		$this->bounce_rate = $bounce_rate;
	}

	/**
	 * @return int
	 */
	public function get_bounce_rate(): int {
		return $this->bounce_rate;
	}

	/**
	 * @param int $visit_duration
	 *
	 * @return void
	 */
	public function set_visit_duration( int $visit_duration ): void {
		$this->visit_duration = $visit_duration;
	}

	/**
	 * @return int
	 */
	public function get_visit_duration(): int {
		return $this->visit_duration;
	}

	/**
	 * @param int $visits
	 *
	 * @return void
	 */
	public function set_visits( int $visits ): void {
		$this->visits = $visits;
	}

	/**
	 * @return int
	 */
	public function get_visits(): int {
		return $this->visits;
	}
}
