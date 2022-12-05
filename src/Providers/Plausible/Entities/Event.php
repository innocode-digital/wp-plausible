<?php

namespace Innocode\Statistics\Providers\Plausible\Entities;

use Innocode\Statistics\Abstracts\AbstractEntity;

class Event extends AbstractEntity {

	/**
	 * @var string
	 */
	protected $domain;
	/**
	 * @var string
	 */
	protected $name = 'pageview';
	/**
	 * @var string
	 */
	protected $url;
	/**
	 * @var string
	 */
	protected $referrer;
	/**
	 * @var int
	 */
	protected $screen_width;

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
	 * @param string $name
	 *
	 * @return void
	 */
	public function set_name( string $name ): void {
		$this->name = $name;
	}

	/**
	 * @return string
	 */
	public function get_name(): string {
		return $this->name;
	}

	/**
	 * @param string $url
	 *
	 * @return void
	 */
	public function set_url( string $url ): void {
		$this->url = $url;
	}

	/**
	 * @return string
	 */
	public function get_url(): string {
		return $this->url;
	}

	/**
	 * @param string $referrer
	 *
	 * @return void
	 */
	public function set_referrer( string $referrer ): void {
		$this->referrer = $referrer;
	}

	/**
	 * @return string
	 */
	public function get_referrer(): string {
		return $this->referrer;
	}

	/**
	 * @param int $screen_width
	 *
	 * @return void
	 */
	public function set_screen_width( int $screen_width ): void {
		$this->screen_width = $screen_width;
	}

	/**
	 * @return int
	 */
	public function get_screen_width(): int {
		return $this->screen_width;
	}
}
