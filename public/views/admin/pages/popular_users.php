<div class="wrap">
	<h1><?php _e( 'Popular Authors', 'innstats' ) ?></h1>
	<div class="innstats-widgets innstats-widgets_<?= esc_attr( WPD\Statistics\Admin::PAGE_POPULAR_USERS ) ?>">
		<?php do_action( 'innstats_admin_page_' . WPD\Statistics\Admin::PAGE_POPULAR_USERS ) ?>
	</div>
</div>
