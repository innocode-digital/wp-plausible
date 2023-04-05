<div class="wrap">
	<h1><?php _e( 'Popular Authors', 'innstats' ) ?></h1>
	<div class="innstats-widgets innstats-widgets_<?= esc_attr( WPD\Statistics\Admin::PAGE_POPULAR_USERS ) ?>">
		<form id="innstats-table-goals" method="get">
			<input type="hidden" name="page" value="innstats-<?= esc_attr( WPD\Statistics\Admin::PAGE_POPULAR_USERS ) ?>">
			<?php do_action( 'innstats_admin_page_' . WPD\Statistics\Admin::PAGE_POPULAR_USERS ) ?>
		</form>
	</div>
</div>
