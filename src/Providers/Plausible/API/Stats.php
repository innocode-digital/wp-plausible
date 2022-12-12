<?php

namespace Innocode\Statistics\Providers\Plausible\API;

use Innocode\Statistics\Abstracts\AbstractEndpoint;
use Innocode\Statistics\Providers\Plausible\Entities\Breakdown;
use Innocode\Statistics\Providers\Plausible\Entities\Metric;
use Innocode\Statistics\Providers\Plausible\Entities\Metrics;
use Innocode\Statistics\Providers\Plausible\Entities\Timeseries;
use Innocode\Statistics\Traits\BearerTokenTrait;
use Innocode\Statistics\Traits\SiteIdTrait;
use Requests;
use WP_Http;

class Stats extends AbstractEndpoint {

	use BearerTokenTrait, SiteIdTrait;

	/**
	 * @return string
	 */
	public function get_namespace(): string {
		return 'api/v1/stats';
	}

	/**
	 * @param string $path
	 * @param array  $query
	 *
	 * @return string
	 */
	protected function url( string $path, array $query = [] ): string {
		return parent::url(
			$path,
			wp_parse_args(
				$query,
				[
					'site_id' => $this->site_id(),
				]
			)
		);
	}

	/**
	 * @param array $query
	 *
	 * @return int|\WP_Error
	 */
	public function realtime_visitors( array $query = [] ) {
		$response = $this->request( Requests::GET, 'realtime/visitors', $query );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$code = wp_remote_retrieve_response_code( $response );
		$body = wp_remote_retrieve_body( $response );

		if ( $code !== WP_Http::OK ) {
			return $this->error( $code, json_decode( $body, true ) );
		}

		return (int) $body;
	}

	/**
	 * @param array $query
	 *
	 * @return Metrics|\WP_Error
	 */
	public function aggregate( array $query = [] ) {
		$response = $this->request( Requests::GET, 'aggregate', $query );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$code    = wp_remote_retrieve_response_code( $response );
		$body    = wp_remote_retrieve_body( $response );
		$metrics = json_decode( $body, true );

		if ( $code !== WP_Http::OK ) {
			return $this->error( $code, $metrics );
		}

		return new Metrics(
			array_map(
				function ( array $metric ) {
					return new Metric( $metric );
				},
				$metrics['results']
			)
		);
	}

	/**
	 * @param array $query
	 *
	 * @return array|\WP_Error
	 */
	public function timeseries( array $query = [] ) {
		$response = $this->request( Requests::GET, 'timeseries', $query );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$code       = wp_remote_retrieve_response_code( $response );
		$body       = wp_remote_retrieve_body( $response );
		$timeseries = json_decode( $body, true );

		if ( $code !== WP_Http::OK ) {
			return $this->error( $code, $timeseries );
		}

		return array_map(
			function ( array $item ) {
				return new Timeseries( $item );
			},
			$timeseries['results']
		);
	}

	/**
	 * @param array $query
	 *
	 * @return array|\WP_Error
	 */
	public function breakdown( array $query = [] ) {
		$response = $this->request( Requests::GET, 'breakdown', $query );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$code      = wp_remote_retrieve_response_code( $response );
		$body      = wp_remote_retrieve_body( $response );
		$breakdown = json_decode( $body, true );

		if ( $code !== WP_Http::OK ) {
			return $this->error( $code, $breakdown );
		}

		return array_map(
			function ( array $item ) {
				return new Breakdown( $item );
			},
			$breakdown['results']
		);
	}
}
