<?php
/**
 * Plugin Name: Innstats
 * Description: .
 * Version: 1.0.0
 * Author: Innocode
 * Author URI: https://innocode.com
 * Tested up to: 6.1.1
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	require_once __DIR__ . '/vendor/autoload.php';
}

use Innocode\Statistics;

define( 'INNSTATS_FILE', __FILE__ );
define( 'INNSTATS_VERSION', '1.0.0' );

$GLOBALS['innstats'] = new Statistics\Plugin( [
	Statistics\Plugin::PROVIDER_PLAUSIBLE, // @TODO: move to constant in case if more providers will be added
] );

if ( ! defined( 'INNSTATS_QUERY_ARG' ) ) {
    define( 'INNSTATS_QUERY_ARG', 'innstats' );
}

$GLOBALS['innstats']->get_query()->set_name( INNSTATS_QUERY_ARG );

if ( $GLOBALS['innstats']->has_provider( Statistics\Plugin::PROVIDER_PLAUSIBLE ) ) {
    if ( ! defined( 'PLAUSIBLE_API_ROOT' ) ) {
        define( 'PLAUSIBLE_API_ROOT', 'https://plausible.io' );
    }

    $GLOBALS['innstats']
        ->get_provider( Statistics\Plugin::PROVIDER_PLAUSIBLE )
        ->get_api()
        ->set_root( PLAUSIBLE_API_ROOT );

	if ( defined( 'PLAUSIBLE_API_KEY' ) ) {
		$GLOBALS['innstats']
			->get_provider( Statistics\Plugin::PROVIDER_PLAUSIBLE )
			->get_api()
            ->get_stats()
            ->set_token( PLAUSIBLE_API_KEY );
	}

	if ( defined( 'PLAUSIBLE_SITES_API_KEY' ) ) {
		$GLOBALS['innstats']
			->get_provider( Statistics\Plugin::PROVIDER_PLAUSIBLE )
			->get_api()
            ->get_site_provisioning()
            ->set_token( PLAUSIBLE_SITES_API_KEY );
	}
}

$GLOBALS['innstats']->run();

if ( ! function_exists( 'innstats' ) ) {
	function innstats() : ?Statistics\Plugin {
		global $innstats;

		return $innstats;
	}
}
