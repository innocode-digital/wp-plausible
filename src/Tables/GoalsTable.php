<?php

namespace WPD\Statistics\Tables;

use WP_List_Table;
use WPD\Statistics\Plugin;
use WPD\Statistics\Providers\Plausible\Entities\Breakdown;

class GoalsTable extends WP_List_Table {

	/**
	 * Constructor.
	 *
	 * @param array $args
	 */
	public function __construct( array $args = [] ) {
		parent::__construct(
			wp_parse_args(
				$args,
				[
					'plural'   => 'goals',
					'singular' => 'goal',
					'ajax'     => true,
				]
			)
		);
	}

	/**
	 * @return bool
	 */
	public function ajax_user_can(): bool {
		return current_user_can( 'manage_options' );
	}

	/**
	 * @return string[]
	 */
	public function get_columns(): array {
		return [
			'page'     => __( 'Page', 'innstats' ),
			'visitors' => __( 'Unique visitors', 'innstats' ),
			'events'   => __( 'Total', 'innstats' ),
			'conv'     => __( 'Conversion rate', 'innstats' ),
		];
	}

	/**
	 * @param array $item
	 * @return string
	 */
	public function column_page( array $item ): string {
		return $item['page'] === 'spinner' ? '<span class="spinner"></span>' : sprintf(
			'<a href="%s" target="_blank">%s</a>',
			home_url( $item['page'] ),
			$item['page']
		);
	}

	/**
	 * @param array $item
	 * @return string
	 */
	public function column_conv( array $item ): string {
		return $item['conv'] !== '' ? number_format( (float) $item['conv'], 1 ) . '%' : '';
	}

	/**
	 * @param array  $item
	 * @param string $column_name
	 * @return string
	 */
	public function column_default( $item, $column_name ): string {
		return $item[ $column_name ] ?? '';
	}

	/**
	 * @return void
	 */
	public function prepare_items(): void {
		$this->_column_headers = [
			$this->get_columns(),
			[],
			[],
			'page',
		];

		if ( wp_doing_ajax() ) {
			$this->items = $this->get_data();
		} else {
			$this->items = [
				[
					'page'     => 'spinner',
					'visitors' => '',
					'events'   => '',
					'conv'     => '',
				],
			];
		}
	}

	/**
	 * @return array
	 */
	protected function get_data(): array {
		// phpcs:ignore Innocode.Security.NonceVerification.Recommended
		$provider = isset( $_REQUEST['provider'] ) && in_array( $_REQUEST['provider'], array_keys( Plugin::PROVIDERS ), true )
			? $_REQUEST['provider'] // phpcs:ignore Innocode.Security.NonceVerification.Recommended
			: Plugin::PROVIDER_PLAUSIBLE;
		// phpcs:ignore Innocode.Security.NonceVerification.Recommended
		$per_page = isset( $_REQUEST['number'] ) ? (int) $_REQUEST['number'] : 20;
		$page     = $this->get_pagenum();
		// phpcs:ignore Innocode.Security.NonceVerification.Recommended
		$search = $_REQUEST['s'] ?? '';

		$api = innstats()->get_provider( $provider );

		if ( ! $api ) {
			return [];
		}

		$aggregate = $api->get_api()->get_stats()->aggregate();

		return array_map(
			function ( Breakdown $item ) use ( $aggregate ) {
				return wp_parse_args(
					[
						'conv' => $item->get_visitors() / $aggregate->get_visitors()->get_value() * 100,
					],
					$item->to_array()
				);
			},
			// @TODO: make it dynamic to handle all methods
			$api->not_found_pages(
				[
					'limit'  => $per_page,
					'page'   => $page,
					'search' => $search,
				]
			)
		);
	}

	/**
	 * @param string $which
	 *
	 * @return void
	 */
	protected function extra_tablenav( $which ): void {
		if ( 'top' === $which ) {
			echo '<div class="alignleft actions">';
		} else {
			echo '<div class="aligncenter actions">';
			echo '<input type="hidden" name="action" value="innstats_admin_goals">';
			echo '<input type="hidden" name="paged" value="1">';
			echo '<input type="hidden" name="number" value="20">';

			wp_nonce_field( 'fetch-list-' . static::get_class(), '_ajax_fetch_list_nonce' );
			printf(
				'<input type="hidden" name="provider" value="%s">',
				esc_attr(
					// phpcs:ignore Innocode.Security.NonceVerification.Recommended
					isset( $_REQUEST['provider'] ) && in_array( $_REQUEST['provider'], array_keys( Plugin::PROVIDERS ), true )
						? $_REQUEST['provider'] // phpcs:ignore Innocode.Security.NonceVerification.Recommended
						: Plugin::PROVIDER_PLAUSIBLE
				)
			);
			printf(
				'<button type="button" id="innstats-load-more-goals" class="button button-secondary">%s</a>',
				__( 'Show more', 'innstats' )
			);
		}

		echo '</div>';
	}

	/**
	 * @return void
	 */
	public function search_help(): void {
		printf(
			'<p class="description">%s</p>',
			__( 'Search by Page (path). You can use one asterisk (*) to represent any number of characters within the same directory like <code>/rule/sub*/more</code> or you can use double asterisks (**) to represent any number of characters even forward slashes like <code>/blog**</code>. Asterisks can be placed on either end or in the middle of any page path URL.', 'innstats' )
		);
	}

	/**
	 * @return string
	 */
	public static function get_class(): string {
		return str_replace( '\\', '', self::class );
	}
}
