<?php

namespace Innocode\Statistics\Traits;

trait SiteIdTrait {

	/**
	 * @return string
	 */
	public function site_id(): string {
		$home_url = wp_parse_url( home_url() );
		$path     = $home_url['path'] ?? '';

		return "{$home_url['host']}$path";
	}
}
