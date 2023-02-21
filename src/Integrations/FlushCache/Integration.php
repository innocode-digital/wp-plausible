<?php

namespace WPD\Statistics\Integrations\FlushCache;

use WPD\Statistics\Interfaces\IntegrationInterface;
use WPD\Statistics\Plugin;

class Integration implements IntegrationInterface {

	/**
	 * @param Plugin $plugin
	 *
	 * @return void
	 */
	public function run( Plugin $plugin ): void {
		if ( function_exists( 'flush_cache_add_button' ) ) {
			flush_cache_add_button(
				__( 'Innstats version', 'innstats' ),
				[ $plugin->get_version(), 'delete' ]
			);
		}
	}
}
