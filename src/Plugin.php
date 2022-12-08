<?php

namespace Innocode\Statistics;

use Innocode\Statistics\Abstracts\AbstractProvider;
use Innocode\Statistics\Providers\Plausible;
use Innocode\Statistics\Traits\SiteIdTrait;
use Innocode\Version\Version;
use WP_Http;
use WP_Term;

final class Plugin {

	use SiteIdTrait;

	const PROVIDER_PLAUSIBLE         = 'plausible';
	const PROVIDERS                  = [
		self::PROVIDER_PLAUSIBLE => Plausible\Provider::class,
	];
	const TEMPLATE_EMBED             = 'embed';
	const TEMPLATE_404               = '404';
	const TEMPLATE_SEARCH            = 'search';
	const TEMPLATE_FRONT_PAGE        = 'front_page';
	const TEMPLATE_HOME              = 'home';
	const TEMPLATE_PRIVACY_POLICY    = 'privacy_policy';
	const TEMPLATE_POST_TYPE_ARCHIVE = 'post_type_archive';
	const TEMPLATE_TAX               = 'tax';
	const TEMPLATE_SINGULAR          = 'singular';
	const TEMPLATE_CATEGORY          = 'category';
	const TEMPLATE_TAG               = 'tag';
	const TEMPLATE_AUTHOR            = 'author';
	const TEMPLATE_DATE              = 'date';
	const TEMPLATE_ARCHIVE           = 'archive';
	/**
	 * With same names as in related WordPress functions e.g. is_singular => 'singular'.
	 *
	 * @note Priority is important and used to determine which template to set.
	 * @note TEMPLATE_SINGULAR includes 'attachment', 'single' and 'page'.
	 *
	 * @const array
	 */
	const TEMPLATES               = [
		self::TEMPLATE_EMBED,
		self::TEMPLATE_404,
		self::TEMPLATE_SEARCH,
		self::TEMPLATE_FRONT_PAGE,
		self::TEMPLATE_HOME,
		self::TEMPLATE_PRIVACY_POLICY,
		self::TEMPLATE_POST_TYPE_ARCHIVE,
		self::TEMPLATE_TAX,
		self::TEMPLATE_SINGULAR,
		self::TEMPLATE_CATEGORY,
		self::TEMPLATE_TAG,
		self::TEMPLATE_AUTHOR,
		self::TEMPLATE_DATE,
		self::TEMPLATE_ARCHIVE,
	];
	const INTEGRATION_FLUSH_CACHE = 'flush_cache';

	/**
	 * @var AbstractProvider[]
	 */
	private $providers = [];
	/**
	 * @var Query
	 */
	private $query;
	/**
	 * @var RESTController
	 */
	private $rest_controller;
	/**
	 * @var Version
	 */
	private $version;
	/**
	 * @var Interfaces\IntegrationInterface[]
	 */
	private $integrations = [];

	/**
	 * @param array $allowed_providers
	 */
	public function __construct( array $allowed_providers = [] ) {
		foreach ( $allowed_providers as $provider ) {
			if ( array_key_exists( $provider, self::PROVIDERS ) ) {
				$class_name                   = self::PROVIDERS[ $provider ];
				$this->providers[ $provider ] = new $class_name();
			}
		}

		$this->query           = new Query();
		$this->rest_controller = new RESTController();
		$this->version         = new Version();

		$this->integrations[ self::INTEGRATION_FLUSH_CACHE ] = new Integrations\FlushCache\Integration();
	}

	/**
	 * @return AbstractProvider[]
	 */
	public function get_providers(): array {
		return $this->providers;
	}

	/**
	 * @return Query
	 */
	public function get_query(): Query {
		return $this->query;
	}

	/**
	 * @return RESTController
	 */
	public function get_rest_controller(): RESTController {
		return $this->rest_controller;
	}

	/**
	 * @return Version
	 */
	public function get_version(): Version {
		return $this->version;
	}

	/**
	 * @return Interfaces\IntegrationInterface[]
	 */
	public function get_integrations(): array {
		return $this->integrations;
	}

	/**
	 * @param string $provider
	 *
	 * @return bool
	 */
	public function has_provider( string $provider ): bool {
		return array_key_exists( $provider, $this->get_providers() );
	}

	/**
	 * @param string $provider
	 *
	 * @return AbstractProvider|null
	 */
	public function get_provider( string $provider ): ?AbstractProvider {
		return $this->has_provider( $provider ) ? $this->get_providers()[ $provider ] : null;
	}

	/**
	 * @return bool
	 */
	public function should_track_ad_blocker(): bool {
		return apply_filters( 'innstats_track_ad_blocker', true );
	}

	/**
	 * @return bool
	 */
	public function should_track_queried_object(): bool {
		return apply_filters( 'innstats_track_queried_object', true );
	}

	/**
	 * @return bool
	 */
	public function should_track_auto_pageviews(): bool {
		return apply_filters( 'innstats_track_auto_pageviews', false );
	}

	/**
	 * @return void
	 */
	public function run(): void {
		$this->get_version()->set_option( 'innstats' );

		register_activation_hook( INNSTATS_FILE, [ $this, 'activate' ] );
		register_deactivation_hook( INNSTATS_FILE, [ $this, 'deactivate' ] );

		add_action( 'plugins_loaded', [ $this, 'run_integrations' ] );
		add_action( 'plugins_loaded', [ $this, 'run_providers' ] );
		add_action( 'admin_init', [ $this, 'lazy_activate' ] );
		add_action( 'wp', [ $this, 'handle_query' ] );
		add_action( 'rest_api_init', [ $this->get_rest_controller(), 'register_routes' ] );
		add_action( 'wp_head', [ $this, 'enqueue_scripts' ], 2 );
		add_action( 'embed_head', [ $this, 'enqueue_scripts' ], 2 );
	}

	/**
	 * @return void
	 */
	public function run_integrations(): void {
		foreach ( $this->get_integrations() as $integration ) {
			$integration->run( $this );
		}
	}

	/**
	 * @return void
	 */
	public function run_providers(): void {
		foreach ( $this->get_providers() as $provider ) {
			$provider->run( $this );
		}
	}

	/**
	 * @return void
	 */
	public function lazy_activate(): void {
		$version = $this->get_version();

		if ( null !== $version() ) {
			return;
		}

		$this->activate();
		$version->bump();
	}

	/**
	 * @return void
	 */
	public function enqueue_scripts(): void {
		wp_enqueue_script(
			'innstats-utils',
			self::url( 'utils' ),
			[],
			INNSTATS_VERSION,
			true
		);

		$data = [
			'domain'               => $this->site_id(),
			'query_var'            => $this->get_query()->get_name(),
			'track_auto_pageviews' => $this->should_track_auto_pageviews(),
			'props'                => apply_filters( 'innstats_props', [] ),
		];

		if ( $this->should_track_queried_object() ) {
			$data['queried_object'] = self::get_queried_object();
		}

		if ( $this->should_track_ad_blocker() ) {
			$data['ad_blocker'] = true;

			wp_enqueue_script(
				'innstats-advert',
				self::url( 'advert' ),
				[],
				INNSTATS_VERSION,
				true
			);
		}

		foreach ( $this->get_providers() as $name => $provider ) {
			if ( ! isset( $data['providers'] ) ) {
				$data['providers'] = [];
			}

			$data['providers'][ $name ] = [
				'api_root' => $provider->get_api()->get_root(),
			];

			wp_enqueue_script(
				"innstats-provider-$name",
				self::url( "providers/$name" ),
				[],
				INNSTATS_VERSION,
				true
			);
		}

		wp_enqueue_script(
			'innstats',
			self::url( 'main' ),
			[],
			INNSTATS_VERSION,
			true
		);

		wp_add_inline_script(
			'innstats-utils',
			'window.innstats = ' . json_encode( $data ) . ';',
			'before'
		);
	}

	/**
	 * @param string $file
	 * @return string
	 */
	public static function url( string $file ): string {
		// Domain mapping processes mu-plugins directory wrong.
		$has_domain_mapping = remove_filter( 'plugins_url', 'domain_mapping_plugins_uri', 1 );
		$suffix             = wp_scripts_get_suffix();

		$url = plugins_url( "public/js/$file$suffix.js", INNSTATS_FILE );

		if ( $has_domain_mapping ) {
			add_filter( 'plugins_url', 'domain_mapping_plugins_uri', 1 );
		}

		return $url;
	}

	/**
	 * @return array
	 */
	public static function get_queried_object(): array {
		$queried_object = [
			'template' => 'index',
			'type'     => '',
			'id'       => 0,
		];

		foreach ( self::TEMPLATES as $template ) {
			if ( call_user_func( "is_$template" ) ) {
				$queried_object['template'] = $template;

				if ( in_array(
					$template,
					[
						self::TEMPLATE_EMBED,
						self::TEMPLATE_SINGULAR,
					],
					true
				) ) {
					// Embed can be 404, avoid empty values.
					if ( ! is_404() ) {
						$queried_object['type'] = get_post_type();
						$queried_object['id']   = get_the_ID();
					}
				} elseif ( $template === self::TEMPLATE_POST_TYPE_ARCHIVE ) {
					$post_type = get_query_var( 'post_type' );

					if ( is_array( $post_type ) ) {
						$post_type = reset( $post_type );
					}

					$post_type_object = get_post_type_object( $post_type );

					if (
						null !== $post_type_object &&
						$post_type_object->has_archive
					) {
						$queried_object['type'] = $post_type;
					}
				} elseif ( in_array(
					$template,
					[
						self::TEMPLATE_TAX,
						self::TEMPLATE_CATEGORY,
						self::TEMPLATE_TAG,
						self::TEMPLATE_AUTHOR,
					],
					true
				) ) {
					$object = get_queried_object();

					if ( $object instanceof WP_Term ) {
						if ( ! in_array(
							$object->taxonomy,
							[
								self::TEMPLATE_CATEGORY,
								self::TEMPLATE_TAG,
							],
							true
						) ) {
							$queried_object['type'] = $object->taxonomy;
						}

						$queried_object['id'] = $object->term_id;
					} else {
						$queried_object['id'] = get_queried_object_id();
					}
				} elseif ( is_day() ) {
					$queried_object['type'] = get_the_date( 'Ymd' );
				} elseif ( is_month() ) {
					$queried_object['type'] = get_the_date( 'Ym' );
				} elseif ( is_year() ) {
					$queried_object['type'] = get_the_date( 'Y' );
				}

				return $queried_object;
			}
		}

		return $queried_object;
	}

	/**
	 * @return void
	 */
	public function handle_query(): void {
		$query = $this->get_query();

		if ( ! $query->is_exists() ) {
			return;
		}

		if ( $this->should_track_queried_object() && $query->value() === 'queried_object' ) {
			wp_send_json( self::get_queried_object(), WP_Http::OK );
		}
	}

	/**
	 * @return void
	 */
	public function activate(): void {
		foreach ( $this->get_providers() as $provider ) {
			$provider->activate( $this );
		}
	}

	/**
	 * @return void
	 */
	public function deactivate(): void {
		foreach ( $this->get_providers() as $provider ) {
			$provider->deactivate( $this );
		}
	}
}
