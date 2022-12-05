<?php

namespace Innocode\Statistics\Providers\Plausible;

use Innocode\Statistics\Abstracts\AbstractProvider;
use Innocode\Statistics\Plugin;
use Innocode\Statistics\Providers\Plausible\Entities\Event;
use Innocode\Statistics\Providers\Plausible\Entities\Site;

class Provider extends AbstractProvider {

	public function __construct() {
		$this->api = new API();
	}

	/**
	 * @param Plugin $plugin
	 *
	 * @return void
	 */
	public function activate( Plugin $plugin ): void {
		$site_provisioning = $this->get_api()->get_site_provisioning();

		if ( $site_provisioning->is_enabled() ) {
			$site = new Site();
			$site->set_domain( $this->site_id() );
			$site->set_timezone( wp_timezone_string() );

			$site = $site_provisioning->create( $site );

			if ( is_wp_error( $site ) ) {
				error_log( $site->get_error_message() );
			}
		}
	}

	/**
	 * @param string $name
	 * @param string $url
	 * @param array  $props
	 *
	 * @return void
	 */
	public function push_event( string $name, string $url, array $props ): void {
		$event = new Event();
		$event->set_name( $name );
		$event->set_domain( $this->site_id() );
		$event->set_url( $url );

		foreach ( $props as $name => $prop ) {
			switch ( $name ) {
				case 'referrer':
					$event->set_referrer( $prop );
					break;
				case 'screen_width':
					$event->set_screen_width( $prop );
					break;
				default:
					$event->set_prop( $name, $prop );
					break;
			}
		}

		$this->get_api()->get_events()->push( $event );
	}

	public function popular_comments( array $query = [] ): array {
		// TODO: Implement popular_comments() method.
	}

	public function popular_posts( array $query = [] ): array {
		// TODO: Implement popular_posts() method.
	}

	public function popular_terms( array $query = [] ): array {
		// TODO: Implement popular_terms() method.
	}

	public function popular_authors( array $query = [] ): array {
		// TODO: Implement popular_authors() method.
	}

	public function popular_urls( array $query = [] ): array {
		// TODO: Implement popular_urls() method.
	}

	public function not_found_pages( array $query = [] ): array {
		// TODO: Implement not_found_pages() method.
	}
}
