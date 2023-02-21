<?php

namespace WPD\Statistics\Providers\Plausible\Entities;

use WPD\Statistics\Abstracts\AbstractEntity;

class Metric extends AbstractEntity {

	/**
	 * @var int
	 */
	protected $value;
	/**
	 * @var int
	 */
	protected $change;

	/**
	 * @param int $value
	 *
	 * @return void
	 */
	public function set_value( int $value ): void {
		$this->value = $value;
	}

	/**
	 * @return int
	 */
	public function get_value(): int {
		return $this->value;
	}

	/**
	 * @param int $change
	 *
	 * @return void
	 */
	public function set_change( int $change ): void {
		$this->change = $change;
	}

	/**
	 * @return int
	 */
	public function get_change(): int {
		return $this->change;
	}
}
