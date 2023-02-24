<?php

namespace WPD\Statistics\Providers\Plausible\API;

use Requests;
use WPD\Statistics\Abstracts\AbstractEndpoint;
use WPD\Statistics\Providers\Plausible\Entities\Event;

class Events extends AbstractEndpoint {

	/**
	 * @return string
	 */
	public function get_namespace(): string {
		return 'api/event';
	}

	/**
	 * @param Event $event
	 *
	 * @return string|\WP_Error
	 */
	public function push( Event $event ) {
		$response = $this->request(
			Requests::POST,
			'',
			[],
			[
				'timeout'  => 1,
				'blocking' => false,
				'headers'  => [
					'X-Forwarded-For' => $_SERVER['SERVER_ADDR'] ?? '127.0.0.1',
					'Content-Type'    => 'application/json',
				],
				'body'     => json_encode( $event->to_array() ),
			]
		);

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		return wp_remote_retrieve_body( $response );
	}
}
