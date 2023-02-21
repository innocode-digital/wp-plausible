<div class="wrap">
	<h1><?php _e( 'Conversions', 'innstats' ) ?></h1>
	<div class="innstats-widgets innstats-widgets_<?= esc_attr( WPD\Statistics\Admin::PAGE_CONVERSIONS ) ?>">
		<?php do_action( 'innstats_admin_page_' . WPD\Statistics\Admin::PAGE_CONVERSIONS ) ?>
	</div>
</div>
