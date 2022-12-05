<?php

namespace Innocode\Statistics\Integrations\FlushCache;

use Innocode\Statistics\Interfaces\IntegrationInterface;
use Innocode\Statistics\Plugin;

class Integration implements IntegrationInterface {

    /**
     * @param Plugin $plugin
     *
     * @return void
     */
    public function run( Plugin $plugin ): void {
        if ( function_exists( 'flush_cache_add_button' ) ) {
            flush_cache_add_button(
                __( 'Innstats version', 'innocode-prerender' ),
                [ $plugin->get_version(), 'delete' ]
            );
        }
    }
}
