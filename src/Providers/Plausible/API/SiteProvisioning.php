<?php

namespace Innocode\Statistics\Providers\Plausible\API;

use Innocode\Statistics\Abstracts\AbstractEndpoint;
use Innocode\Statistics\Providers\Plausible\Entities\Goal;
use Innocode\Statistics\Providers\Plausible\Entities\SharedLink;
use Innocode\Statistics\Providers\Plausible\Entities\Site;
use Innocode\Statistics\Providers\Plausible\Entities\Status;
use Innocode\Statistics\Traits\BearerTokenTrait;
use Requests;
use WP_Http;

class SiteProvisioning extends AbstractEndpoint {

	use BearerTokenTrait;

	/**
	 * @return string
	 */
	public function get_namespace(): string {
		return 'api/v1/sites';
	}

	/**
	 * @return bool
	 */
	public function is_enabled(): bool {
		return null !== $this->get_token();
	}

	/**
	 * @param Site $site
	 *
	 * @return Site|WP_Error
	 */
	public function create( Site $site ) {
		$response = $this->request(
			Requests::POST,
			'',
			[],
			[
				'body' => $site->to_array(),
			]
		);

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$code = wp_remote_retrieve_response_code( $response );
		$body = wp_remote_retrieve_body( $response );
		$site = json_decode( $body, true );

		if ( $code !== WP_Http::OK ) {
			return $this->error( $code, $site );
		}

		return new Site( $site );
	}

	/**
	 * @param string $site_id
	 *
	 * @return Site|WP_Error
	 */
	public function get( string $site_id ) {
		$response = $this->request( Requests::GET, $site_id );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$code = wp_remote_retrieve_response_code( $response );
		$body = wp_remote_retrieve_body( $response );
		$site = json_decode( $body, true );

		if ( $code !== WP_Http::OK ) {
			return $this->error( $code, $site );
		}

		return new Site( $site );
	}

	/**
	 * @param string $site_id
	 *
	 * @return Status|WP_Error
	 */
	public function delete( string $site_id ) {
		$response = $this->request( Requests::DELETE, $site_id );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$code   = wp_remote_retrieve_response_code( $response );
		$body   = wp_remote_retrieve_body( $response );
		$status = json_decode( $body, true );

		if ( $code !== WP_Http::OK ) {
			return $this->error( $code, $status );
		}

		return new Status( $status );
	}

	/**
	 * @param string $site_id
	 * @param string $name
	 *
	 * @return SharedLink|WP_Error
	 */
	public function create_shared_link( string $site_id, string $name ) {
		$shared_link = new SharedLink();
		$shared_link->set_site_id( $site_id );
		$shared_link->set_name( $name );

		$response = $this->request(
			Requests::PUT,
			'shared-links',
			[],
			[
				'body' => $shared_link->to_array(),
			]
		);

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$code        = wp_remote_retrieve_response_code( $response );
		$body        = wp_remote_retrieve_body( $response );
		$shared_link = json_decode( $body, true );

		if ( $code !== WP_Http::OK ) {
			return $this->error( $code, $shared_link );
		}

		return new SharedLink( $shared_link );
	}

	/**
	 * @param Goal $goal
	 *
	 * @return Goal|WP_Error
	 */
	public function create_goal( Goal $goal ) {
		$response = $this->request(
			Requests::PUT,
			'goals',
			[],
			[
				'body' => $goal->to_array(),
			]
		);

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$code = wp_remote_retrieve_response_code( $response );
		$body = wp_remote_retrieve_body( $response );
		$goal = json_decode( $body, true );

		if ( $code !== WP_Http::OK ) {
			return $this->error( $code, $goal );
		}

		return new Goal( $goal );
	}

	/**
	 * @param string $site_id
	 * @param int    $goal_id
	 *
	 * @return Status|WP_Error
	 */
	public function delete_goal( string $site_id, int $goal_id ) {
		$response = $this->request(
			Requests::DELETE,
			"goals/$goal_id",
			[],
			[
				'body' => [
					'site_id' => $site_id,
				],
			]
		);

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$code   = wp_remote_retrieve_response_code( $response );
		$body   = wp_remote_retrieve_body( $response );
		$status = json_decode( $body, true );

		if ( $code !== WP_Http::OK ) {
			return $this->error( $code, $status );
		}

		return new Status( $status );
	}
}
