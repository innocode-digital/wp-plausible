<?php

namespace Innocode\Statistics\Interfaces;

use Innocode\Statistics\Plugin;

interface IntegrationInterface {

	/**
	 * @param Plugin $plugin
	 *
	 * @return void
	 */
	public function run( Plugin $plugin ): void;
}
