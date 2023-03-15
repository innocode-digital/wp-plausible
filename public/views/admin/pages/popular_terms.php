<div class="wrap">
	<h1><?php _e( 'Popular Terms', 'innstats' ) ?></h1>
	<div class="innstats-widgets innstats-widgets_<?= esc_attr( WPD\Statistics\Admin::PAGE_POPULAR_TERMS ) ?>">
		<?php do_action( 'innstats_admin_page_' . WPD\Statistics\Admin::PAGE_POPULAR_TERMS ) ?>
	</div>
</div>
