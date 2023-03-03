<?php

namespace WPD\Statistics\Traits;

trait SiteIdTrait {

	/**
	 * @return string
	 */
	public function site_id(): string {
		$home_url = wp_parse_url( home_url() );
		$host     = substr( $home_url['host'], 0, 4 ) === 'www.' ? substr( $home_url['host'], 4 ) : $home_url['host'];
		$path     = $home_url['path'] ?? '';

		return "$host$path";
	}
}
