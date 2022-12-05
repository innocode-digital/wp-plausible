<?php

namespace Innocode\Statistics\Providers\Plausible\Entities;

use Innocode\Statistics\Abstracts\AbstractEntity;

class Site extends AbstractEntity {

	/**
	 * @var string
	 */
	protected $domain;
	/**
	 * @var string
	 */
	protected $timezone;

	/**
	 * @param string $domain
	 *
	 * @return void
	 */
	public function set_domain( string $domain ): void {
		$this->domain = $domain;
	}

	/**
	 * @return string
	 */
	public function get_domain(): string {
		return $this->domain;
	}

	/**
	 * @param string $timezone
	 *
	 * @return void
	 */
	public function set_timezone( string $timezone ): void {
		$this->timezone = $timezone;
	}

	/**
	 * @return string
	 */
	public function get_timezone(): string {
		return $this->timezone;
	}
}
