<?php

namespace WPD\Statistics\Tables;

use WP_List_Table;
use WPD\Statistics\Plugin;
use WPD\Statistics\Providers\Plausible\Entities\Breakdown;

class GoalsTable extends WP_List_Table {

	/**
	 * @var string
	 */
	protected $label;
	/**
	 * @var string
	 */
	protected $api_method;
	/**
	 * @var string
	 */
	protected $type;

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

		$this->set_label( __( 'Page', 'innstats' ) );
	}

	/**
	 * @param string $label
	 *
	 * @return void
	 */
	public function set_label( string $label ): void {
		$this->label = $label;
	}

	/**
	 * @return string
	 */
	public function get_label(): string {
		return $this->label;
	}

	/**
	 * @param string $api_method
	 *
	 * @return void
	 */
	public function set_api_method( string $api_method ): void {
		$this->api_method = $api_method;
	}

	/**
	 * @return string
	 */
	public function get_api_method(): string {
		return $this->api_method;
	}

	/**
	 * @param string $type
	 *
	 * @return void
	 */
	public function set_type( string $type ): void {
		$this->type = $type;
	}

	/**
	 * @return string|null
	 */
	public function get_type(): ?string {
		return $this->type;
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
			'page'     => $this->get_label(),
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
		if ( $item['page'] === 'spinner' ) {
			return '<span class="spinner"></span>';
		}

		$path             = $item['page'];
		$home_url         = rtrim( home_url(), '/' );
		$home_path        = parse_url( $home_url, PHP_URL_PATH );
		$home_path_length = strlen( $home_path );

		if ( $home_path !== null && substr( $path, 0, $home_path_length ) === $home_path ) {
			$path = substr( $path, $home_path_length );
		}

		return sprintf(
			'<a href="%s" target="_blank">%s</a>',
			home_url( $path ),
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

		$aggregate  = $api->get_api()->get_stats()->aggregate();
		$api_method = $this->get_api_method();

		if ( ! $api_method || ! method_exists( $api, $api_method ) ) {
			return [];
		}

		$type      = $this->get_type();
		$breakdown = $type ? $api->{$api_method}(
			$type,
			[
				'limit'  => $per_page,
				'page'   => $page,
				'search' => $search,
			]
		) : $api->{$api_method}(
			[
				'limit'  => $per_page,
				'page'   => $page,
				'search' => $search,
			]
		);

		return array_map(
			function ( Breakdown $item ) use ( $aggregate ) {
				return wp_parse_args(
					[
						'conv' => $item->get_visitors() / $aggregate->get_visitors()->get_value() * 100,
					],
					$item->to_array()
				);
			},
			$breakdown
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

	/**
	 * @return void
	 */
	public function _js_vars(): void { // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
		parent::_js_vars();

		printf(
			"<script>list_args.api_method = %s;</script>\n",
			wp_json_encode( $this->get_api_method() )
		);

		$type = $this->get_type();

		if ( $type ) {
			printf(
				"<script>list_args.type = %s;</script>\n",
				wp_json_encode( $type )
			);
		}
	}
}
