<?php

namespace Innocode\Statistics\Providers\Plausible\Entities;

use Innocode\Statistics\Abstracts\AbstractEntity;

class Metrics extends AbstractEntity {

	/**
	 * @var Metric
	 */
	protected $visitors;
	/**
	 * @var Metric
	 */
	protected $pageviews;
	/**
	 * @var Metric
	 */
	protected $bounce_rate;
	/**
	 * @var Metric
	 */
	protected $visit_duration;
	/**
	 * @var Metric
	 */
	protected $visits;
	/**
	 * @var Metric
	 */
	protected $events;

	/**
	 * @param Metric $visitors
	 *
	 * @return void
	 */
	public function set_visitors( Metric $visitors ): void {
		$this->visitors = $visitors;
	}

	/**
	 * @return Metric
	 */
	public function get_visitors(): Metric {
		return $this->visitors;
	}

	/**
	 * @param Metric $pageviews
	 *
	 * @return void
	 */
	public function set_pageviews( Metric $pageviews ): void {
		$this->pageviews = $pageviews;
	}

	/**
	 * @return Metric
	 */
	public function get_pageviews(): Metric {
		return $this->pageviews;
	}

	/**
	 * @param Metric $bounce_rate
	 *
	 * @return void
	 */
	public function set_bounce_rate( Metric $bounce_rate ): void {
		$this->bounce_rate = $bounce_rate;
	}

	/**
	 * @return Metric
	 */
	public function get_bounce_rate(): Metric {
		return $this->bounce_rate;
	}

	/**
	 * @param Metric $visit_duration
	 *
	 * @return void
	 */
	public function set_visit_duration( Metric $visit_duration ): void {
		$this->visit_duration = $visit_duration;
	}

	/**
	 * @return Metric
	 */
	public function get_visit_duration(): Metric {
		return $this->visit_duration;
	}

	/**
	 * @param Metric $visits
	 *
	 * @return void
	 */
	public function set_visits( Metric $visits ): void {
		$this->visits = $visits;
	}

	/**
	 * @return Metric
	 */
	public function get_visits(): Metric {
		return $this->visits;
	}

	/**
	 * @param Metric $events
	 *
	 * @return void
	 */
	public function set_events( Metric $events ): void {
		$this->events = $events;
	}

	/**
	 * @return Metric
	 */
	public function get_events(): Metric {
		return $this->events;
	}
}
