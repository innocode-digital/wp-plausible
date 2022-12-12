<?php

namespace Innocode\Statistics\Providers\Plausible;

use Innocode\Statistics\Abstracts\AbstractAPI;
use Innocode\Statistics\Providers\Plausible\API\Events;
use Innocode\Statistics\Providers\Plausible\API\SiteProvisioning;
use Innocode\Statistics\Providers\Plausible\API\Stats;

class API extends AbstractAPI {

	/**
	 * @var Stats
	 */
	protected $stats;
	/**
	 * @var Events
	 */
	protected $events;
	/**
	 * @var SiteProvisioning
	 */
	protected $site_provisioning;

	/**
	 * Initializes endpoints.
	 */
	public function __construct() {
		$this->stats             = new Stats();
		$this->events            = new Events();
		$this->site_provisioning = new SiteProvisioning();
	}

	/**
	 * @return Stats
	 */
	public function get_stats(): Stats {
		return $this->stats;
	}

	/**
	 * @return Events
	 */
	public function get_events(): Events {
		return $this->events;
	}

	/**
	 * @return SiteProvisioning
	 */
	public function get_site_provisioning(): SiteProvisioning {
		return $this->site_provisioning;
	}

	/**
	 * @return \Innocode\Statistics\Abstracts\AbstractEndpoint[]
	 */
	public function get_endpoints(): array {
		return [
			$this->get_stats(),
			$this->get_events(),
			$this->get_site_provisioning(),
		];
	}
}
