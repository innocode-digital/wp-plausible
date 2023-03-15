<?php

namespace WPD\Statistics;

use WP_Post_Type;
use WP_Taxonomy;
use WPD\Statistics\Tables\GoalsTable;
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
		add_action( 'wp_ajax_innstats_admin_goals', [ $this, 'goals_ajax' ] );
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

			add_action( "innstats_admin_page_$page", [ $this, "page_$page" ] );
		}
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
	protected function enqueue_goals_styles(): void {
		wp_enqueue_style(
			'innstats-pages-goals',
			Plugin::url( 'pages/goals', 'css' ),
			[],
			INNSTATS_VERSION
		);
	}

	/**
	 * @return void
	 */
	public function enqueue_popularity_styles(): void {
		$this->enqueue_goals_styles();
	}

	/**
	 * @return void
	 */
	public function enqueue_not_found_pages_styles(): void {
		$this->enqueue_goals_styles();
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
	 * @return void
	 */
	protected function enqueue_goals_scripts(): void {
		wp_enqueue_script(
			'innstats-pages-goals',
			Plugin::url( 'pages/goals' ),
			[ 'jquery', 'wp-dom-ready', 'wp-lists', 'innstats-api' ],
			INNSTATS_VERSION,
			true
		);
	}

	/**
	 * @return void
	 */
	public function enqueue_popularity_scripts(): void {
		$this->enqueue_goals_scripts();
	}

	/**
	 * @return void
	 */
	public function enqueue_not_found_pages_scripts(): void {
		$this->enqueue_goals_scripts();
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
	public function page_general(): void {
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

	/**
	 * @return void
	 */
	public function page_popularity(): void {
		if ( ! class_exists( 'WP_List_Table' ) ) {
			require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
		}

		$post_types = get_post_types( [ 'public' => true ], 'objects' );
		$taxonomies = get_taxonomies( [ 'public' => true ], 'objects' );

		$tabs = array_values(
			apply_filters(
				'innstats_popularity_tabs',
				array_merge(
					array_map(
						function ( $post_type ) {
							return [ "post_type-$post_type->name", $post_type ];
						},
						$post_types
					),
					array_map(
						function ( $taxonomy ) {
							return [ "taxonomy-$taxonomy->name", $taxonomy ];
						},
						$taxonomies
					)
				)
			)
		);

		if ( empty( $tabs ) ) {
			printf(
				'<p>%s</p>',
				esc_html__( 'No public post types or taxonomies found.', 'innstats' )
			);

			return;
		}

		echo '<nav class="nav-tab-wrapper">';

		$menu_page_url = menu_page_url( 'innstats-' . self::PAGE_POPULARITY, false );
		$active_tab    = isset( $_GET['tab'] ) && in_array( $_GET['tab'], array_column( $tabs, 0 ), true )
			? $_GET['tab']
			: $tabs[0][0];

		foreach ( $tabs as $tab ) {
			list( $tab, $object ) = $tab;

			printf(
				'<a href="%s" class="nav-tab %s">%s</a>',
				esc_url( add_query_arg( 'tab', $tab, $menu_page_url ) ),
				$active_tab === $tab ? 'nav-tab-active' : '',
				esc_html( $object->label )
			);
		}

		echo '</nav>';

		foreach ( $tabs as $tab ) {
			list( $tab, $object ) = $tab;

			if ( $tab === $active_tab ) {
				$table = new GoalsTable();

				if ( $object instanceof WP_Post_Type ) {
					$table->set_primary_column( 'post' );
					$table->set_api_method( 'popular_posts' );
				} elseif ( $object instanceof WP_Taxonomy ) {
					$table->set_primary_column( 'term' );
					$table->set_api_method( 'popular_terms' );
				}

				$table->set_type( $object->name );
				$table->set_primary_label( $object->labels->singular_name );
				$table->prepare_items();

				echo '<form id="innstats-table-goals" method="get">';

				$table->search_box( $object->labels->search_items, 'goals' );
				$table->search_help();
				$table->display();

				echo '</form>';

				break;
			}
		}
	}

	/**
	 * @return void
	 */
	public function page_not_found_pages(): void {
		if ( ! class_exists( 'WP_List_Table' ) ) {
			require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
		}

		$table = new GoalsTable();
		$table->set_api_method( 'not_found_pages' );
		$table->prepare_items();

		echo '<form id="innstats-table-goals" method="get">';

		$table->search_box( __( 'Search Pages', 'innstats' ), 'goals' );
		$table->search_help();
		$table->display();

		echo '</form>';
	}

	/**
	 * @return void
	 */
	public function page_conversions(): void {
		printf(
			'<h2>%s</h2><p>%s</p>',
			esc_html__( 'It\'s not available yet', 'innstats' ),
			esc_html__( 'The metrics you\'re looking for aren\'t associated with your site. If you\'re interesting in such functionality like measuring of how many people visit a specific section of your site then feel free to contact your development team or hosting provider.', 'innstats' )
		);
	}

	/**
	 * @return void
	 */
	public function goals_ajax(): void {
		$list_args = $_GET['list_args'] ?? [];

		if (
			! isset(
				$list_args['class'],
				$list_args['screen']['id'],
				$list_args['screen']['base'],
				$list_args['api_method'],
				$list_args['primary_column']
			)
		) {
			wp_die( 0 );
		}

		check_ajax_referer( "fetch-list-{$list_args['class']}", '_ajax_fetch_list_nonce' );

		$table = new GoalsTable(
			[
				'screen' => $list_args['screen']['id'],
			]
		);
		$table->set_primary_column( $list_args['primary_column'] );
		$table->set_api_method( $list_args['api_method'] );

		if ( isset( $list_args['type'] ) ) {
			$table->set_type( $list_args['type'] );
		}

		if ( ! $table->ajax_user_can() ) {
			wp_die( -1 );
		}

		$table->ajax_response();

		wp_die( 0 );
	}
}
