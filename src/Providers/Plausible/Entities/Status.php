<?php

namespace WPD\Statistics\Providers\Plausible\Entities;

use WPD\Statistics\Abstracts\AbstractEntity;

class Status extends AbstractEntity {

	/**
	 * @var bool
	 */
	protected $deleted;

	/**
	 * @param bool $deleted
	 *
	 * @return void
	 */
	public function set_deleted( bool $deleted ): void {
		$this->deleted = $deleted;
	}

	/**
	 * @return bool
	 */
	public function get_deleted(): bool {
		return $this->deleted;
	}
}
