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
	 * @param array  $data
	 *
	 * @return string
	 */
	protected function url( string $path, array $data = [] ): string {
		return parent::url(
			$path,
			wp_parse_args(
				$data,
				[
					'site_id' => $this->site_id(),
				]
			)
		);
	}

	/**
	 * @param array $data
	 *
	 * @return int|\WP_Error
	 */
	public function realtime_visitors( array $data = [] ) {
		$response = $this->request( Requests::GET, 'realtime/visitors', $data );

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
	 * @param array $data
	 *
	 * @return Metrics|\WP_Error
	 */
	public function aggregate( array $data = [] ) {
		$response = $this->request( Requests::GET, 'aggregate', $data );

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
	 * @param array $data
	 *
	 * @return array|\WP_Error
	 */
	public function timeseries( array $data = [] ) {
		$response = $this->request( Requests::GET, 'timeseries', $data );

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
	 * @param array $data
	 *
	 * @return array|\WP_Error
	 */
	public function breakdown( array $data = [] ) {
		$response = $this->request( Requests::GET, 'breakdown', $data );

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
