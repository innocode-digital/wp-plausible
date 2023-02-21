<?php

namespace WPD\Statistics;

use WPD\Statistics\Traits\ViewsTrait;

final class Admin {

	use ViewsTrait;

	const PAGE_GENERAL         = 'general';
	const PAGE_POPULARITY      = 'popularity';
	const PAGE_NOT_FOUND_PAGES = 'not_found_pages';
	const PAGE_CONVERSIONS     = 'conversions';

	/**
	 * @var array Hook Suffixes.
	 */
	private $pages = [];

	/**
	 * @return array
	 */
	public function get_pages(): array {
		return $this->pages;
	}

	/**
	 * Initializes dashboard functionality.
	 */
	public function run() {
		add_action( 'admin_menu', [ $this, 'add_pages' ] );
		add_action( 'admin_init', [ $this, 'init' ] );
	}

	/**
	 * @return void
	 */
	public function add_pages(): void {
		$this->pages[ self::PAGE_GENERAL ] = add_menu_page(
			__( 'Analytics - Innstats', 'innstats' ),
			__( 'Innstats', 'innstats' ),
			'manage_options',
			'innstats-' . self::PAGE_GENERAL,
			function (): void {
				$this->page( self::PAGE_GENERAL );
			},
			'dashicons-analytics',
			3
		);

		foreach ( [
			self::PAGE_POPULARITY      => __( 'Popularity', 'innstats' ),
			self::PAGE_NOT_FOUND_PAGES => __( 'Not Found Pages', 'innstats' ),
			self::PAGE_CONVERSIONS     => __( 'Conversions', 'innstats' ),
		] as $name => $title ) {
			$this->pages[ $name ] = add_submenu_page(
				'innstats-' . self::PAGE_GENERAL,
				sprintf( '%s - %s', $title, __( 'Innstats', 'innstats' ) ),
				$title,
				'manage_options',
				"innstats-$name",
				function () use ( $name ): void {
					$this->page( $name );
				}
			);
		}
	}

	/**
	 * @param string $name
	 *
	 * @return void
	 */
	private function page( string $name ): void {
		$this->view( "admin/pages/$name" );
	}

	/**
	 * @return void
	 */
	public function init(): void {
		foreach ( $this->get_pages() as $page => $hook_suffix ) {
			add_action( "admin_print_scripts-$hook_suffix", [ $this, 'enqueue_scripts' ] );

			if ( method_exists( $this, "enqueue_{$page}_styles" ) ) {
				add_action( "admin_print_styles-$hook_suffix", [ $this, "enqueue_{$page}_styles" ] );
			}

			if ( method_exists( $this, "enqueue_{$page}_scripts" ) ) {
				add_action( "admin_print_scripts-$hook_suffix", [ $this, "enqueue_{$page}_scripts" ] );
			}
		}

		add_action(
			'innstats_admin_page_' . self::PAGE_GENERAL,
			function () {
				// @phpcs:ignore Innocode.Security.EscapeOutput.OutputNotEscaped
				echo $this->section(
					'general',
					__( 'General', 'innstats' ),
					[
						'misc',
						'country',
						'timeseries',
						'bounce_rate',
						'visit_duration',
					]
				);

				// @phpcs:ignore Innocode.Security.EscapeOutput.OutputNotEscaped
				echo $this->section(
					'top_pages',
					__( 'Top Pages', 'innstats' ),
					[
						'page',
						'entry_page',
						'exit_page',
					]
				);

				// @phpcs:ignore Innocode.Security.EscapeOutput.OutputNotEscaped
				echo $this->section(
					'top_sources',
					__( 'Top Sources', 'innstats' ),
					[
						'source',
						'utm_medium',
						'utm_source',
						'utm_campaign',
						'utm_term',
						'utm_content',
					]
				);

				// @phpcs:ignore Innocode.Security.EscapeOutput.OutputNotEscaped
				echo $this->section(
					'devices_and_browsers',
					__( 'Devices & Browsers', 'innstats' ),
					[
						'device',
						'browser',
						'os',
						'device_pixel_ratio',
						'language',
						'ad_blocker',
					]
				);
			}
		);

		add_action( 'innstats_admin_page_' . self::PAGE_POPULARITY, [ $this, 'widgets_popularity' ] );
		add_action( 'innstats_admin_page_' . self::PAGE_NOT_FOUND_PAGES, [ $this, 'widget_not_found_pages' ] );
	}

	/**
	 * @return void
	 */
	public function enqueue_general_styles(): void {
		wp_enqueue_style(
			'innstats-pages-dashboard',
			Plugin::url( 'pages/dashboard', 'css' ),
			[],
			INNSTATS_VERSION
		);
	}

	/**
	 * @return void
	 */
	public function enqueue_scripts(): void {
		wp_enqueue_script(
			'innstats-api',
			Plugin::url( 'api' ),
			[ 'wp-api-request' ],
			INNSTATS_VERSION,
			true
		);

		wp_add_inline_script(
			'innstats-api',
			'window.innstats = ' . json_encode(
				[
					'home_url' => esc_url_raw( home_url() ),
				]
			) . ';',
			'before'
		);
	}

	/**
	 * @return void
	 */
	public function enqueue_general_scripts(): void {
		// @phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
		wp_enqueue_script(
			'chart.js',
			'https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.1.1/chart.umd.min.js',
			[],
			null,
			true
		);

		// @phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
		wp_enqueue_script(
			'chartjs-chart-geo',
			'https://cdn.jsdelivr.net/npm/chartjs-chart-geo@4.1.2/build/index.umd.min.js',
			[],
			null,
			true
		);

		wp_enqueue_script(
			'innstats-charts',
			Plugin::url( 'charts' ),
			[],
			INNSTATS_VERSION,
			true
		);

		wp_enqueue_script(
			'innstats-pages-dashboard',
			Plugin::url( 'pages/dashboard' ),
			[ 'wp-dom-ready', 'innstats-api' ],
			INNSTATS_VERSION,
			true
		);
	}

	/**
	 * @param string $name
	 * @param string $title
	 * @param array  $widgets
	 * @return string
	 */
	private function section( string $name, string $title, array $widgets ): string {
		return sprintf(
			'<section id="innstats-section-%1$s" class="innstats-section innstats-section_%1$s"><h2 class="innstats-section__title">%2$s</h2><div class="innstats-section__content">%3$s</div></section>',
			esc_attr( $name ),
			esc_html( $title ),
			implode(
				'',
				array_map(
					function ( string $widget ): string {
						return $this->widget( $widget );
					},
					$widgets
				)
			)
		);
	}

	/**
	 * @param string $name
	 *
	 * @return string
	 */
	private function widget( string $name ): string {
		return sprintf(
			'<div class="innstats-widget innstats-widget_%1$s postbox"><div class="inside"><%2$s id="innstats-widget-%1$s"></%2$s></div></div>',
			esc_attr( $name ),
			'misc' === $name ? 'div' : 'canvas'
		);
	}

	/**
	 * @return void
	 */
	public function widgets_popularity(): void {
		$post_types = get_post_types( [ 'public' => true ], 'objects' );
		$taxonomies = get_taxonomies( [ 'public' => true ], 'objects' );
	}

	/**
	 * @return void
	 */
	public function widget_not_found_pages(): void {
		$this->widget( 'not_found_pages' );
	}
}
