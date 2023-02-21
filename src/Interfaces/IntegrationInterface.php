<?php

namespace WPD\Statistics\Interfaces;

use WPD\Statistics\Plugin;

interface IntegrationInterface {

	/**
	 * @param Plugin $plugin
	 *
	 * @return void
	 */
	public function run( Plugin $plugin ): void;
}
