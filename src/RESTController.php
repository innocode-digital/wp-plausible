<?php

namespace WPD\Statistics;

use WP_Error;
use WP_Http;
use WP_REST_Controller;
use WP_REST_Request;
use WP_REST_Server;
use WPD\Statistics\Providers\Plausible\Entities\Breakdown;
use WPD\Statistics\Providers\Plausible\Entities\Timeseries;

class RESTController extends WP_REST_Controller {

	/**
	 * Initializes base properties.
	 */
	public function __construct() {
		$this->namespace = 'wpd/v1';
		$this->rest_base = 'statistics';
	}

	/**
	 * @return void
	 */
	public function register_routes(): void {
		register_rest_route(
			$this->namespace,
			"/$this->rest_base/realtime_visitors",
			[
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => [ $this, 'realtime_visitors' ],
				'permission_callback' => [ $this, 'get_items_permissions_check' ],
				'args'                => $this->get_realtime_visitors_args(),
			]
		);

		register_rest_route(
			$this->namespace,
			"/$this->rest_base/aggregate",
			[
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => [ $this, 'aggregate' ],
				'permission_callback' => [ $this, 'get_items_permissions_check' ],
				'args'                => $this->get_aggregate_args(),
			]
		);

		register_rest_route(
			$this->namespace,
			"/$this->rest_base/timeseries",
			[
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => [ $this, 'timeseries' ],
				'permission_callback' => [ $this, 'get_items_permissions_check' ],
				'args'                => $this->get_timeseries_args(),
			]
		);

		register_rest_route(
			$this->namespace,
			"/$this->rest_base/breakdown",
			[
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => [ $this, 'breakdown' ],
				'permission_callback' => [ $this, 'get_items_permissions_check' ],
				'args'                => $this->get_breakdown_args(),
			]
		);
	}

	/**
	 * @param WP_REST_Request $request
	 *
	 * @return true|WP_Error
	 */
	public function get_items_permissions_check( $request ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			return new WP_Error(
				'rest_forbidden',
				__( 'Sorry, you are not allowed to do that.' ),
				[ 'status' => rest_authorization_required_code() ]
			);
		}

		return true;
	}

	/**
	 * @param WP_REST_Request $request
	 *
	 * @return \WP_REST_Response|WP_Error
	 */
	public function realtime_visitors( WP_REST_Request $request ) {
		$provider          = innstats()->get_provider( $request['provider'] );
		$realtime_visitors = $provider->get_api()->get_stats()->realtime_visitors();

		if ( is_wp_error( $realtime_visitors ) ) {
			$realtime_visitors->add_data(
				[
					'status' => WP_Http::INTERNAL_SERVER_ERROR,
				]
			);

			return $realtime_visitors;
		}

		return rest_ensure_response( $realtime_visitors );
	}

	/**
	 * @param WP_REST_Request $request
	 *
	 * @return \WP_REST_Response|WP_Error
	 */
	public function aggregate( WP_REST_Request $request ) {
		$params   = $request->get_params();
		$provider = innstats()->get_provider( $params['provider'] );

		unset( $params['provider'] );

		$aggregate = $provider->get_api()->get_stats()->aggregate( $params );

		if ( is_wp_error( $aggregate ) ) {
			$aggregate->add_data(
				[
					'status' => WP_Http::INTERNAL_SERVER_ERROR,
				]
			);

			return $aggregate;
		}

		return rest_ensure_response( $aggregate->to_array() );
	}

	/**
	 * @param WP_REST_Request $request
	 *
	 * @return \WP_REST_Response|WP_Error
	 */
	public function timeseries( WP_REST_Request $request ) {
		$params   = $request->get_params();
		$provider = innstats()->get_provider( $params['provider'] );

		unset( $params['provider'] );

		$timeseries = $provider->get_api()->get_stats()->timeseries( $params );

		if ( is_wp_error( $timeseries ) ) {
			$timeseries->add_data(
				[
					'status' => WP_Http::INTERNAL_SERVER_ERROR,
				]
			);

			return $timeseries;
		}

		return rest_ensure_response(
			array_map(
				function ( Timeseries $item ) {
					return $item->to_array();
				},
				$timeseries
			)
		);
	}

	/**
	 * @param WP_REST_Request $request
	 *
	 * @return \WP_REST_Response|WP_Error
	 */
	public function breakdown( WP_REST_Request $request ) {
		$params   = $request->get_params();
		$provider = innstats()->get_provider( $params['provider'] );

		unset( $params['provider'] );

		$breakdown = $provider->get_api()->get_stats()->breakdown( $params );

		if ( is_wp_error( $breakdown ) ) {
			$breakdown->add_data(
				[
					'status' => WP_Http::INTERNAL_SERVER_ERROR,
				]
			);

			return $breakdown;
		}

		return rest_ensure_response(
			array_map(
				function ( Breakdown $item ) {
					return $item->to_array();
				},
				$breakdown
			)
		);
	}

	/**
	 * @return array
	 */
	protected function get_realtime_visitors_args(): array {
		return [
			'provider' => $this->get_provider_arg(),
		];
	}

	/**
	 * @return array
	 */
	protected function get_aggregate_args(): array {
		return [
			'provider' => $this->get_provider_arg(),
			'period'   => $this->get_period_arg(),
			'metrics'  => $this->get_metrics_arg(),
			'compare'  => [
				'type' => 'string',
				'enum' => [ 'previous_period' ],
			],
			'filters'  => [
				'type' => 'string',
			],
		];
	}

	/**
	 * @return array
	 */
	protected function get_timeseries_args(): array {
		return [
			'provider' => $this->get_provider_arg(),
			'period'   => $this->get_period_arg(),
			'filters'  => [
				'type' => 'string',
			],
			'metrics'  => $this->get_metrics_arg(),
			'interval' => [
				'type' => 'string',
				'enum' => [ 'date', 'month' ],
			],
		];
	}

	/**
	 * @return array
	 */
	protected function get_breakdown_args(): array {
		return [
			'provider' => $this->get_provider_arg(),
			'property' => [
				'type'     => 'string',
				'required' => true,
			],
			'period'   => $this->get_period_arg(),
			'metrics'  => $this->get_metrics_arg(),
			'limit'    => [
				'type'    => 'integer',
				'default' => 100,
				'minimum' => 1,
				'maximum' => 1000,
			],
			'page'     => [
				'type'    => 'integer',
				'minimum' => 1,
			],
			'filters'  => [
				'type' => 'string',
			],
		];
	}

	/**
	 * @return array
	 */
	protected function get_provider_arg(): array {
		return [
			'type'     => 'string',
			'required' => true,
			'default'  => Plugin::PROVIDER_PLAUSIBLE,
			'enum'     => [ Plugin::PROVIDER_PLAUSIBLE ],
		];
	}

	/**
	 * @return array
	 */
	protected function get_period_arg(): array {
		return [
			'type'    => 'string',
			'default' => '7d',
			'enum'    => [ 'day', '7d', '30d', 'month', '6mo', '12mo', 'custom' ],
		];
	}

	/**
	 * @return array
	 */
	protected function get_metrics_arg(): array {
		return [
			'type'  => 'array',
			'items' => [
				'type' => 'string',
				'enum' => [
					'visitors',
					'pageviews',
					'bounce_rate',
					'visit_duration',
					'visits',
					'events',
				],
			],
		];
	}
}
