<?php

namespace WPD\Statistics\Providers\Plausible\Entities;

use WPD\Statistics\Abstracts\AbstractEntity;

class SharedLink extends AbstractEntity {

	/**
	 * @var string
	 */
	protected $site_id;
	/**
	 * @var string
	 */
	protected $name;
	/**
	 * @var string
	 */
	protected $url;

	/**
	 * @param string $site_id
	 *
	 * @return void
	 */
	public function set_site_id( string $site_id ): void {
		$this->site_id = $site_id;
	}

	/**
	 * @return string
	 */
	public function get_site_id(): string {
		return $this->site_id;
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
}
