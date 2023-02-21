<div class="wrap">
	<h1><?php _e( 'Not Found Pages', 'innstats' ) ?></h1>
	<div class="innstats-widgets innstats-widgets_<?= esc_attr( WPD\Statistics\Admin::PAGE_NOT_FOUND_PAGES ) ?>">
		<?php do_action( 'innstats_admin_page_' . WPD\Statistics\Admin::PAGE_NOT_FOUND_PAGES ) ?>
	</div>
</div>
