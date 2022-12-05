<?php

namespace Innocode\Statistics\Providers\Plausible\Entities;

use Innocode\Statistics\Abstracts\AbstractEntity;

class Goal extends AbstractEntity {

	/**
	 * @var string
	 */
	protected $site_id;
	/**
	 * @var string
	 */
	protected $domain;
	/**
	 * @var int
	 */
	protected $id;
	/**
	 * @var string
	 */
	protected $goal_type = 'event';
	/**
	 * @var string|null
	 */
	protected $event_name;
	/**
	 * @var string|null
	 */
	protected $page_path;

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
	 * @param int $id
	 *
	 * @return void
	 */
	public function set_id( int $id ): void {
		$this->id = $id;
	}

	/**
	 * @return int
	 */
	public function get_id(): int {
		return $this->id;
	}

	/**
	 * @param string $goal_type
	 *
	 * @return void
	 */
	protected function set_goal_type( string $goal_type ): void {
		$this->goal_type = $goal_type;
	}

	/**
	 * @return void
	 */
	public function use_page_goal_type(): void {
		$this->set_goal_type( 'page' );
	}

	/**
	 * @return string
	 */
	public function get_goal_type(): string {
		return $this->goal_type;
	}

	/**
	 * @param string $event_name
	 *
	 * @return void
	 */
	public function set_event_name( string $event_name ): void {
		$this->event_name = $event_name;
	}

	/**
	 * @return string|null
	 */
	public function get_event_name(): ?string {
		return $this->event_name;
	}

	/**
	 * @param string $page_path
	 *
	 * @return void
	 */
	public function set_page_path( string $page_path ): void {
		$this->page_path = $page_path;
	}

	/**
	 * @return string|null
	 */
	public function get_page_path(): ?string {
		return $this->page_path;
	}
}
