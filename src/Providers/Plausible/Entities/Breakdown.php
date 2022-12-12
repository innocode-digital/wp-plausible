<?php

namespace Innocode\Statistics\Providers\Plausible\Entities;

use Innocode\Statistics\Abstracts\AbstractEntity;

class Breakdown extends AbstractEntity {

	/**
	 * @var string
	 */
	protected $name;
	/**
	 * @var string
	 */
	protected $page;
	/**
	 * @var string
	 */
	protected $entry_page;
	/**
	 * @var string
	 */
	protected $exit_page;
	/**
	 * @var string
	 */
	protected $source;
	/**
	 * @var string
	 */
	protected $referrer;
	/**
	 * @var string
	 */
	protected $utm_medium;
	/**
	 * @var string
	 */
	protected $utm_source;
	/**
	 * @var string
	 */
	protected $utm_campaign;
	/**
	 * @var string
	 */
	protected $utm_content;
	/**
	 * @var string
	 */
	protected $utm_term;
	/**
	 * @var string
	 */
	protected $device;
	/**
	 * @var string
	 */
	protected $browser;
	/**
	 * @var string
	 */
	protected $browser_version;
	/**
	 * @var string
	 */
	protected $os;
	/**
	 * @var string
	 */
	protected $os_version;
	/**
	 * @var string
	 */
	protected $country;
	/**
	 * @var string
	 */
	protected $region;
	/**
	 * @var string
	 */
	protected $city;
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
	 * @var int
	 */
	protected $events;
	/**
	 * @var \WP_Comment|\WP_Post|\WP_Term|\WP_User
	 */
	protected $object;

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
	 * @param string $page
	 *
	 * @return void
	 */
	public function set_page( string $page ): void {
		$this->page = $page;
	}

	/**
	 * @return string
	 */
	public function get_page(): string {
		return $this->page;
	}

	/**
	 * @param string $entry_page
	 *
	 * @return void
	 */
	public function set_entry_page( string $entry_page ): void {
		$this->entry_page = $entry_page;
	}

	/**
	 * @return string
	 */
	public function get_entry_page(): string {
		return $this->entry_page;
	}

	/**
	 * @param string $exit_page
	 *
	 * @return void
	 */
	public function set_exit_page( string $exit_page ): void {
		$this->exit_page = $exit_page;
	}

	/**
	 * @return string
	 */
	public function get_exit_page(): string {
		return $this->exit_page;
	}

	/**
	 * @param string $source
	 *
	 * @return void
	 */
	public function set_source( string $source ): void {
		$this->source = $source;
	}

	/**
	 * @return string
	 */
	public function get_source(): string {
		return $this->source;
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
	 * @param string $utm_medium
	 *
	 * @return void
	 */
	public function set_utm_medium( string $utm_medium ): void {
		$this->utm_medium = $utm_medium;
	}

	/**
	 * @return string
	 */
	public function get_utm_medium(): string {
		return $this->utm_medium;
	}

	/**
	 * @param string $utm_source
	 *
	 * @return void
	 */
	public function set_utm_source( string $utm_source ): void {
		$this->utm_source = $utm_source;
	}

	/**
	 * @return string
	 */
	public function get_utm_source(): string {
		return $this->utm_source;
	}

	/**
	 * @param string $utm_campaign
	 *
	 * @return void
	 */
	public function set_utm_campaign( string $utm_campaign ): void {
		$this->utm_campaign = $utm_campaign;
	}

	/**
	 * @return string
	 */
	public function get_utm_campaign(): string {
		return $this->utm_campaign;
	}

	/**
	 * @param string $utm_content
	 *
	 * @return void
	 */
	public function set_utm_content( string $utm_content ): void {
		$this->utm_content = $utm_content;
	}

	/**
	 * @return string
	 */
	public function get_utm_content(): string {
		return $this->utm_content;
	}

	/**
	 * @param string $utm_term
	 *
	 * @return void
	 */
	public function set_utm_term( string $utm_term ): void {
		$this->utm_term = $utm_term;
	}

	/**
	 * @return string
	 */
	public function get_utm_term(): string {
		return $this->utm_term;
	}

	/**
	 * @param string $device
	 *
	 * @return void
	 */
	public function set_device( string $device ): void {
		$this->device = $device;
	}

	/**
	 * @return string
	 */
	public function get_device(): string {
		return $this->device;
	}

	/**
	 * @param string $browser
	 *
	 * @return void
	 */
	public function set_browser( string $browser ): void {
		$this->browser = $browser;
	}

	/**
	 * @return string
	 */
	public function get_browser(): string {
		return $this->browser;
	}

	/**
	 * @param string $browser_version
	 *
	 * @return void
	 */
	public function set_browser_version( string $browser_version ): void {
		$this->browser_version = $browser_version;
	}

	/**
	 * @return string
	 */
	public function get_browser_version(): string {
		return $this->browser_version;
	}

	/**
	 * @param string $os
	 *
	 * @return void
	 */
	public function set_os( string $os ): void {
		$this->os = $os;
	}

	/**
	 * @return string
	 */
	public function get_os(): string {
		return $this->os;
	}

	/**
	 * @param string $os_version
	 *
	 * @return void
	 */
	public function set_os_version( string $os_version ): void {
		$this->os_version = $os_version;
	}

	/**
	 * @return string
	 */
	public function get_os_version(): string {
		return $this->os_version;
	}

	/**
	 * @param string $country
	 *
	 * @return void
	 */
	public function set_country( string $country ): void {
		$this->country = $country;
	}

	/**
	 * @return string
	 */
	public function get_country(): string {
		return $this->country;
	}

	/**
	 * @param string $region
	 *
	 * @return void
	 */
	public function set_region( string $region ): void {
		$this->region = $region;
	}

	/**
	 * @return string
	 */
	public function get_region(): string {
		return $this->region;
	}

	/**
	 * @param string $city
	 *
	 * @return void
	 */
	public function set_city( string $city ): void {
		$this->city = $city;
	}

	/**
	 * @return string
	 */
	public function get_city(): string {
		return $this->city;
	}

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

	/**
	 * @param int $events
	 *
	 * @return void
	 */
	public function set_events( int $events ): void {
		$this->events = $events;
	}

	/**
	 * @return int
	 */
	public function get_events(): int {
		return $this->visits;
	}

	/**
	 * @param \WP_Comment|\WP_Post|\WP_Term|\WP_User $object
	 *
	 * @return void
	 */
	public function set_object( $object ): void {
		$this->object = $object;
	}

	/**
	 * @return \WP_Comment|\WP_Post|\WP_Term|\WP_User
	 */
	public function get_object() {
		return $this->object;
	}
}
