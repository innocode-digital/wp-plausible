<?php

namespace Innocode\Statistics;

use WP_REST_Controller;

class RESTController extends WP_REST_Controller {

	public function __construct() {
		$this->namespace = 'innocode/v1';
		$this->rest_base = 'statistics';
	}

	/**
	 * @return void
	 */
	public function register_routes(): void {

	}
}
