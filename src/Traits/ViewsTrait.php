<?php

namespace Innocode\Statistics\Traits;

trait ViewsTrait {

	/**
	 * @param string $file
	 *
	 * @return void
	 */
	protected function view( string $file ): void {
		require_once dirname( INNSTATS_FILE ) . "/public/views/$file.php";
	}
}
