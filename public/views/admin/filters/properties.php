<?php
foreach ( [
	'event:page'         => esc_html__( 'Page', 'innstats' ),
	'country'            => esc_html__( 'Country', 'innstats' ),
	'entry_page'         => esc_html__( 'Entry Page', 'innstats' ),
	'exit_page'          => esc_html__( 'Exit Page', 'innstats' ),
	'source'             => esc_html__( 'Source', 'innstats' ),
	'utm_medium'         => esc_html__( 'UTM Medium', 'innstats' ),
	'utm_source'         => esc_html__( 'UTM Source', 'innstats' ),
	'utm_campaign'       => esc_html__( 'UTM Campaign', 'innstats' ),
	'utm_term'           => esc_html__( 'UTM Term', 'innstats' ),
	'utm_content'        => esc_html__( 'UTM Content', 'innstats' ),
	'device'             => esc_html__( 'Device', 'innstats' ),
	'browser'            => esc_html__( 'Browser', 'innstats' ),
	'os'                 => esc_html__( 'Operating System', 'innstats' ),
	'device_pixel_ratio' => esc_html__( 'Device Pixel Ratio', 'innstats' ),
	'language'           => esc_html__( 'Language', 'innstats' ),
	'ad_blocker'         => esc_html__( 'Ad Blocker', 'innstats' ),
] as $key => $value ) :
	if ( isset( $_GET[ $key ] ) && $_GET[ $key ] !== '' ) :
		?>
		<div class="innstats-filter button">
			<span class="innstats-filter__label"><?= esc_html( $value ) ?></span>
			<span class="innstats-filter__operator"><?php _e( 'is', 'innstats' ) ?></span>
			<span class="innstats-filter__value"><?= esc_html( $_GET[ $key ] ) ?></span>
			<button type="button"  class="innstats-filter__button dashicons dashicons-no">
				<?php esc_html_e( 'Remove', 'innstats' ) ?>
			</button>
			<input type="hidden" name="<?= esc_attr( $key ) ?>" value="<?= esc_attr( $_GET[ $key ] ) ?>">
		</div>
		<?php
	endif;
endforeach;
