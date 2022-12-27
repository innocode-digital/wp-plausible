<?php

namespace Innocode\Statistics;

use Innocode\Statistics\Traits\ViewsTrait;

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
		$this->pages[] = add_menu_page(
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
			$this->pages[] = add_submenu_page(
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
		foreach ( $this->get_pages() as $page ) {
			add_action( "admin_print_scripts-$page", [ $this, 'enqueue_scripts' ] );
		}

		foreach ( [
			'current_visitors',
			'general',
			'sources',
			'campaigns',
			'pages',
			'devices',
			'browsers',
			'ad_blocker',
			'os',
		] as $name ) {
			add_action( 'innstats_admin_page_' . self::PAGE_GENERAL, [ $this, "widget_$name" ] );
		}

		add_action( 'innstats_admin_page_' . self::PAGE_POPULARITY, [ $this, 'widgets_popularity' ] );
		add_action( 'innstats_admin_page_' . self::PAGE_NOT_FOUND_PAGES, [ $this, 'widget_not_found_pages' ] );
	}

	/**
	 * @return void
	 */
	public function enqueue_scripts(): void {
		// @phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
		wp_enqueue_script(
			'chart.js',
			'https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.1.1/chart.umd.min.js',
			[],
			null,
			true
		);

		wp_enqueue_script(
			'innstats-admin',
			Plugin::url( 'admin' ),
			[ 'wp-dom-ready', 'wp-api-request', 'chart.js' ],
			INNSTATS_VERSION,
			true
		);
	}

	/**
	 * @param string $name
	 *
	 * @return void
	 */
	private function widget( string $name ): void {
		printf(
			'<canvas id="innstats-widget-%1$s" class="innstats-widget innstats-widget_%1$s"></canvas>',
			esc_attr( $name )
		);
	}

	/**
	 * @return void
	 */
	public function widget_current_visitors(): void {
		$this->widget( 'current_visitors' );
	}

	/**
	 * @return void
	 */
	public function widget_general(): void {
		$this->widget( 'general' );
	}

	/**
	 * @return void
	 */
	public function widget_sources(): void {
		$this->widget( 'sources' );
	}

	/**
	 * @return void
	 */
	public function widget_campaigns(): void {
		$this->widget( 'campaigns' );
	}

	/**
	 * @return void
	 */
	public function widget_pages(): void {
		$this->widget( 'pages' );
	}

	/**
	 * @return void
	 */
	public function widget_devices(): void {
		$this->widget( 'devices' );
	}

	/**
	 * @return void
	 */
	public function widget_browsers(): void {
		$this->widget( 'browsers' );
	}

	/**
	 * @return void
	 */
	public function widget_ad_blocker(): void {
		$this->widget( 'ad_blocker' );
	}

	/**
	 * @return void
	 */
	public function widget_os(): void {
		$this->widget( 'os' );
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
