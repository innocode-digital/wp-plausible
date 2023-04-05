<div class="wrap">
	<h1><?php _e( 'Analytics', 'innstats' ) ?></h1>
	<div class="innstats-widgets innstats-widgets_<?= esc_attr( WPD\Statistics\Admin::PAGE_GENERAL ) ?>">
		<form method="get">
			<input type="hidden" name="page" value="innstats-<?= esc_attr( WPD\Statistics\Admin::PAGE_GENERAL ) ?>">
			<?php do_action( 'innstats_admin_page_' . WPD\Statistics\Admin::PAGE_GENERAL ) ?>
		</form>
	</div>
</div>
