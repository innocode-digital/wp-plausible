<?php

namespace Innocode\Statistics\Abstracts;

use WP_Error;

abstract class AbstractEndpoint {

	/**
	 * @var string
	 */
	protected $api_root = '';

	/**
	 * @param string $api_root
	 *
	 * @return void
	 */
	public function set_api_root( string $api_root ): void {
		$this->api_root = $api_root;
	}

	/**
	 * @return string
	 */
	public function get_api_root(): string {
		return $this->api_root;
	}

	/**
	 * @return string
	 */
	abstract public function get_namespace(): string;

	/**
	 * @param string $path
	 * @param array  $query
	 *
	 * @return string
	 */
	protected function url( string $path, array $query = [] ): string {
		$url = sprintf(
			'%s/%s',
			rtrim( $this->get_api_root(), '/' ),
			trim( $this->get_namespace(), '/' )
		);

		$path = ltrim( $path, '/' );

		if ( $path ) {
			$url .= "/$path";
		}

		$query = http_build_query( $query );

		if ( $query ) {
			$url .= "?$query";
		}

		return $url;
	}

	/**
	 * @return string|null
	 */
	public function auth(): ?string {
		return null;
	}

	/**
	 * @param string $method
	 * @param array  $args
	 *
	 * @return array
	 */
	protected function request_args( string $method, array $args = [] ): array {
		$args = wp_parse_args(
			$args,
			[
				'method'    => $method,
				'sslverify' => false,
			]
		);

		if ( ! isset( $args['headers']['Authorization'] ) ) {
			$auth = $this->auth();

			if ( null !== $auth ) {
				if ( ! isset( $args['headers'] ) ) {
					$args['headers'] = [];
				}

				$args['headers']['Authorization'] = $this->auth();
			}
		}

		return $args;
	}

	/**
	 * @param string $method
	 * @param string $path
	 * @param array  $query
	 * @param array  $args
	 *
	 * @return array|WP_Error
	 */
	protected function request( string $method, string $path, array $query = [], array $args = [] ) {
		return wp_remote_request( $this->url( $path, $query ), $this->request_args( $method, $args ) );
	}

	/**
	 * @param int   $code
	 * @param mixed $error
	 * @return WP_Error
	 */
	protected function error( int $code, $error ): WP_Error {
		$message = '';

		if ( is_string( $error ) ) {
			$message = $error;
		} elseif ( is_array( $error ) && isset( $error['error'] ) && is_string( $error['error'] ) ) {
			$message = $error['error'];
		}

		return new WP_Error( $code, $message );
	}
}
