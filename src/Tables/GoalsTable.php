<?php

namespace WPD\Statistics\Tables;

use WP_List_Table;
use WPD\Statistics\Plugin;
use WPD\Statistics\Providers\Plausible\Entities\Breakdown;

class GoalsTable extends WP_List_Table {

	/**
	 * @var string
	 */
	protected $primary_column = 'page';
	/**
	 * @var string
	 */
	protected $primary_label;
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

		$this->set_primary_label( __( 'Page', 'innstats' ) );
	}

	/**
	 * @param string $primary_column
	 *
	 * @return void
	 */
	public function set_primary_column( string $primary_column ): void {
		$this->primary_column = $primary_column;
	}

	/**
	 * @return string
	 */
	public function get_primary_column(): string {
		return $this->primary_column;
	}

	/**
	 * @param string $primary_label
	 *
	 * @return void
	 */
	public function set_primary_label( string $primary_label ): void {
		$this->primary_label = $primary_label;
	}

	/**
	 * @return string
	 */
	public function get_primary_label(): string {
		return $this->primary_label;
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
			$this->get_primary_column() => $this->get_primary_label(),
			'visitors'                  => __( 'Unique visitors', 'innstats' ),
			'events'                    => __( 'Total', 'innstats' ),
			'conv'                      => __( 'Conversion rate', 'innstats' ),
		];
	}

	/**
	 * @return string
	 */
	protected function column_spinner(): string {
		return '<span class="spinner"></span>';
	}

	/**
	 * @param array $item
	 * @return string
	 */
	public function column_page( array $item ): string {
		if ( $item['page'] === 'spinner' ) {
			return $this->column_spinner();
		}

		if ( ! is_string( $item['page'] ) ) {
			return '';
		}

		return sprintf(
			'<strong class="row-title">%s</strong>',
			esc_html( $item['page'] )
		);
	}

	/**
	 * @param array $item
	 *
	 * @return string
	 */
	public function column_comment( array $item ): string {
		if ( $item['comment'] === 'spinner' ) {
			return $this->column_spinner();
		}

		return '';
	}

	/**
	 * @param array $item
	 *
	 * @return string
	 */
	public function column_post( array $item ): string {
		if ( $item['post'] === 'spinner' ) {
			return $this->column_spinner();
		}

		$post = get_post( $item['post'] );

		if ( ! $post ) {
			return '';
		}

		$title = _draft_or_post_title( $post );

		return sprintf(
			'<strong class="row-title">%s</strong>',
			$title
		);
	}

	/**
	 * @param array $item
	 *
	 * @return string
	 */
	public function column_term( array $item ): string {
		if ( $item['term'] === 'spinner' ) {
			return $this->column_spinner();
		}

		$term = get_term_by( 'term_taxonomy_id', $item['term'] );

		if ( ! $term ) {
			return '';
		}

		return sprintf(
			'<strong class="row-title">%s</strong>',
			$term->name
		);
	}

	/**
	 * @param array $item
	 *
	 * @return string
	 */
	public function column_user( array $item ): string {
		if ( $item['user'] === 'spinner' ) {
			return $this->column_spinner();
		}

		$user = get_userdata( $item['user'] );

		if ( ! $user ) {
			return '';
		}

		return sprintf(
			'<strong class="row-title">%s</strong>',
			$user->display_name
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
	 * @param array  $item
	 * @param string $column_name
	 * @param string $primary
	 *
	 * @return string
	 */
	protected function handle_row_actions( $item, $column_name, $primary ): string {
		if ( $primary !== $column_name ) {
			return parent::handle_row_actions( $item, $column_name, $primary );
		}

		if ( $column_name === 'page' ) {
			return $this->handle_page_row_actions( $item, $column_name, $primary );
		}

		if ( $column_name === 'post' ) {
			return $this->handle_post_row_actions( $item, $column_name, $primary );
		}

		if ( $column_name === 'term' ) {
			return $this->handle_term_row_actions( $item, $column_name, $primary );
		}

		if ( $column_name === 'user' ) {
			return $this->handle_user_row_actions( $item, $column_name, $primary );
		}

		return parent::handle_row_actions( $item, $column_name, $primary );
	}

	/**
	 * @param array  $item
	 * @param string $column_name
	 * @param string $primary
	 *
	 * @return string
	 */
	protected function handle_page_row_actions( array $item, string $column_name, string $primary ): string {
		if ( ! is_string( $item['page'] ) || $item['page'] === 'spinner' ) {
			return parent::handle_row_actions( $item, $column_name, $primary );
		}

		$path             = $item['page'];
		$home_url         = rtrim( home_url(), '/' );
		$home_path        = parse_url( $home_url, PHP_URL_PATH );
		$home_path_length = strlen( $home_path );
		$actions          = [];

		if ( $home_path !== null && substr( $path, 0, $home_path_length ) === $home_path ) {
			$path = substr( $path, $home_path_length );
		}

		$actions['view'] = sprintf(
			'<a href="%s" rel="bookmark" aria-label="%s">%s</a>',
			home_url( $path ),
			/* translators: %s: Page path. */
			esc_attr( sprintf( __( 'View &#8220;%s&#8221;' ), $item['page'] ) ),
			__( 'View' )
		);

		return $this->row_actions( $actions );
	}

	/**
	 * @param array  $item
	 * @param string $column_name
	 * @param string $primary
	 *
	 * @return string
	 */
	protected function handle_post_row_actions( array $item, string $column_name, string $primary ): string {
		if ( ! is_numeric( $item['post'] ) || $item['post'] === 'spinner' ) {
			return parent::handle_row_actions( $item, $column_name, $primary );
		}

		$post = get_post( $item['post'] );

		if ( ! $post ) {
			return parent::handle_row_actions( $item, $column_name, $primary );
		}

		$post_type_object = get_post_type_object( $post->post_type );
		$can_edit_post    = current_user_can( 'edit_post', $post->ID );
		$title            = _draft_or_post_title( $post );
		$actions          = [
			/* translators: %s: Post ID. */
			'id' => sprintf( __( 'ID: %d', 'innstats' ), $post->ID ),
		];

		if ( $can_edit_post ) {
			$actions['edit'] = sprintf(
				'<a href="%s" aria-label="%s">%s</a>',
				get_edit_post_link( $post->ID ),
				/* translators: %s: Post title. */
				esc_attr( sprintf( __( 'Edit &#8220;%s&#8221;' ), $title ) ),
				__( 'Edit' )
			);
		}

		if ( is_post_type_viewable( $post_type_object ) ) {
			if ( in_array( $post->post_status, [ 'pending', 'draft', 'future' ], true ) ) {
				if ( $can_edit_post ) {
					$preview_link    = get_preview_post_link( $post );
					$actions['view'] = sprintf(
						'<a href="%s" rel="bookmark" aria-label="%s">%s</a>',
						esc_url( $preview_link ),
						/* translators: %s: Post title. */
						esc_attr( sprintf( __( 'Preview &#8220;%s&#8221;' ), $title ) ),
						__( 'Preview' )
					);
				}
			} elseif ( 'trash' !== $post->post_status ) {
				$actions['view'] = sprintf(
					'<a href="%s" rel="bookmark" aria-label="%s">%s</a>',
					get_permalink( $post->ID ),
					/* translators: %s: Post title. */
					esc_attr( sprintf( __( 'View &#8220;%s&#8221;' ), $title ) ),
					__( 'View' )
				);
			}
		}

		return $this->row_actions( $actions );
	}

	/**
	 * @param array  $item
	 * @param string $column_name
	 * @param string $primary
	 *
	 * @return string
	 */
	protected function handle_term_row_actions( array $item, string $column_name, string $primary ): string {
		if ( ! is_numeric( $item['term'] ) || $item['term'] === 'spinner' ) {
			return parent::handle_row_actions( $item, $column_name, $primary );
		}

		$term = get_term_by( 'term_taxonomy_id', $item['term'] );

		if ( ! $term ) {
			return parent::handle_row_actions( $item, $column_name, $primary );
		}

		$actions = [
			/* translators: %s: Term ID. */
			'id' => sprintf( __( 'ID: %d', 'innstats' ), $term->term_id ),
		];

		if ( current_user_can( 'edit_term', $term->term_id ) ) {
			$actions['edit'] = sprintf(
				'<a href="%s" aria-label="%s">%s</a>',
				esc_url(
					add_query_arg(
						'wp_http_referer',
						rawurlencode( wp_unslash( $_SERVER['REQUEST_URI'] ) ),
						get_edit_term_link( $term )
					)
				),
				/* translators: %s: Taxonomy term name. */
				esc_attr( sprintf( __( 'Edit &#8220;%s&#8221;' ), $term->name ) ),
				__( 'Edit' )
			);
		}

		if ( is_term_publicly_viewable( $term ) ) {
			$actions['view'] = sprintf(
				'<a href="%s" aria-label="%s">%s</a>',
				get_term_link( $term ),
				/* translators: %s: Taxonomy term name. */
				esc_attr( sprintf( __( 'View &#8220;%s&#8221; archive' ), $term->name ) ),
				__( 'View' )
			);
		}

		return $this->row_actions( $actions );
	}

	/**
	 * @param array  $item
	 * @param string $column_name
	 * @param string $primary
	 *
	 * @return string
	 */
	protected function handle_user_row_actions( array $item, string $column_name, string $primary ): string {
		if ( ! is_numeric( $item['user'] ) || $item['user'] === 'spinner' ) {
			return parent::handle_row_actions( $item, $column_name, $primary );
		}

		$user = get_userdata( $item['user'] );

		if ( ! $user ) {
			return parent::handle_row_actions( $item, $column_name, $primary );
		}

		$actions = [
			/* translators: %s: User ID. */
			'id' => sprintf( __( 'ID: %d', 'innstats' ), $user->ID ),
		];

		if ( current_user_can( 'edit_user', $user->ID ) ) {
			$actions['edit'] = sprintf(
				'<a href="%s" aria-label="%s">%s</a>',
				esc_url(
					add_query_arg(
						'wp_http_referer',
						rawurlencode( wp_unslash( $_SERVER['REQUEST_URI'] ) ),
						get_edit_user_link( $user->ID )
					)
				),
				/* translators: %s: User name. */
				esc_attr( sprintf( __( 'Edit &#8220;%s&#8221;' ), $user->display_name ) ),
				__( 'Edit' )
			);
		}

		$author_posts_url = get_author_posts_url( $user->ID );

		if ( $author_posts_url ) {
			$actions['view'] = sprintf(
				'<a href="%s" aria-label="%s">%s</a>',
				esc_url( $author_posts_url ),
				/* translators: %s: Author's display name. */
				esc_attr( sprintf( __( 'View posts by %s' ), $user->display_name ) ),
				__( 'View' )
			);
		}

		return $this->row_actions( $actions );
	}

	/**
	 * @return void
	 */
	public function prepare_items(): void {
		$this->_column_headers = [
			$this->get_columns(),
			[],
			[],
			$this->get_primary_column(),
		];

		if ( wp_doing_ajax() ) {
			$this->items = $this->get_data();
		} else {
			$this->items = [
				[
					$this->get_primary_column() => 'spinner',
					'visitors'                  => '',
					'events'                    => '',
					'conv'                      => '',
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

		$type  = $this->get_type();
		$query = [
			'limit'  => $per_page,
			'page'   => $page,
			'search' => $search,
		];

		if ( $type ) {
			$breakdown = $api->{$api_method}( $type, $query );
		} else {
			$breakdown = $api->{$api_method}( $query );
		}

		$total_visitors = $aggregate->get_visitors()->get_value();

		return array_map(
			function ( Breakdown $item ) use ( $total_visitors ) {
				$arr         = $item->to_array();
				$arr['conv'] = $item->get_visitors() / $total_visitors * 100;

				if ( isset( $arr['props']['id'] ) ) {
					$arr[ $this->get_primary_column() ] = (int) $arr['props']['id'];
				}

				return $arr;
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
			"<script>\nlist_args.api_method = %s;\nlist_args.primary_column = %s;\n</script>\n",
			wp_json_encode( $this->get_api_method() ),
			wp_json_encode( $this->get_primary_column() )
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
