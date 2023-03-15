<?php

namespace WPD\Statistics;

final class Plan {

	/**
	 * @var string
	 */
	private $slug;

	/**
	 * @var string
	 */
	private $name;

	/**
	 * @var bool
	 */
	private $is_visible = true;

	/**
	 * @var array
	 */
	private $features = [];

	/**
	 * @param string $slug
	 * @param string $name
	 */
	public function __construct( string $slug, string $name ) {
		$this->slug = $slug;
		$this->name = $name;
	}

	/**
	 * @return string
	 */
	public function get_name(): string {
		return $this->name;
	}

	/**
	 * @return string
	 */
	public function get_slug(): string {
		return $this->slug;
	}

	/**
	 * @param bool $is_visible
	 *
	 * @return void
	 */
	public function set_visible( bool $is_visible ): void {
		$this->is_visible = $is_visible;
	}

	/**
	 * @return bool
	 */
	public function is_visible(): bool {
		return $this->is_visible;
	}

	/**
	 * @param string $feature
	 *
	 * @return void
	 */
	public function add_feature( string $feature ): void {
		$this->features[] = $feature;
	}

	/**
	 * @return array
	 */
	public function get_features(): array {
		return $this->features;
	}

	/**
	 * @param string $feature
	 *
	 * @return bool
	 */
	public function has_feature( string $feature ): bool {
		return in_array( $feature, $this->features, true );
	}

	/**
	 * @return string
	 */
	public function __toString(): string {
		return $this->is_visible()
			? sprintf(
				' <span class="innstats-badge innstats-badge_%s">%s</span>',
				$this->get_slug(),
				$this->get_name()
			)
			: '';
	}
}
