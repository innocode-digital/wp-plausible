<?php
$period        = $_GET['period'] ?? '7d'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
$start_date    = $_GET['start_date'] ?? ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
$end_date      = $_GET['end_date'] ?? ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
$today         = date_i18n( 'Y-m-d' );
$yesterday     = date_i18n( 'Y-m-d', strtotime( 'yesterday' ) );
$monday        = date_i18n( 'Y-m-d', strtotime( 'monday this week' ) );
$year_start    = date_i18n( 'Y-01-01' );
$date_disabled = $period === 'custom' ? '' : 'disabled';
?>
<label for="filter-by-period" class="screen-reader-text"><?php esc_html_e( 'Filter by period', 'innstats' ) ?></label>
<select id="filter-by-period" name="period">
	<optgroup label="<?php esc_attr_e( 'Day', 'innstats' ) ?>">
		<option value="day" <?php selected( $period, 'day' ) ?>><?php esc_html_e( 'Today', 'innstats' ) ?></option>
		<option value="<?= esc_attr( $yesterday ) ?>" <?php selected( $period, $yesterday ) ?>><?php esc_html_e( 'Yesterday', 'innstats' ) ?></option>
	</optgroup>
	<optgroup label="<?php esc_attr_e( 'Week', 'innstats' ) ?>">
		<option value="<?= esc_attr( $monday ) ?>" <?php selected( $period, $monday ) ?>><?php esc_html_e( 'This week', 'innstats' ) ?></option>
		<option value="7d" <?php selected( $period, '7d' ) ?>><?php esc_html_e( 'Last 7 days', 'innstats' ) ?></option>
	</optgroup>
	<optgroup label="<?php esc_attr_e( 'Month', 'innstats' ) ?>">
		<option value="month" <?php selected( $period, 'month' ) ?>><?php esc_html_e( 'This month', 'innstats' ) ?></option>
		<option value="30d" <?php selected( $period, '30d' ) ?>><?php esc_html_e( 'Last 30 days', 'innstats' ) ?></option>
	</optgroup>
	<optgroup label="<?php esc_attr_e( 'Year', 'innstats' ) ?>">
		<option value="<?= esc_attr( $year_start ) ?>" <?php selected( $period, $year_start ) ?>><?php esc_html_e( 'This year', 'innstats' ) ?></option>
		<option value="6mo" <?php selected( $period, '6mo' ) ?>><?php esc_html_e( 'Last 6 months', 'innstats' ) ?></option>
		<option value="12mo" <?php selected( $period, '12mo' ) ?>><?php esc_html_e( 'Last 12 months', 'innstats' ) ?></option>
	</optgroup>
	<optgroup label="<?php esc_attr_e( 'Other', 'innstats' ) ?>">
		<option value="custom" <?php selected( $period, 'custom' ) ?>><?php esc_html_e( 'Custom', 'innstats' ) ?></option>
	</optgroup>
</select>
<label for="filter-by-start_date" class="screen-reader-text"><?php esc_html_e( 'Filter by start date', 'innstats' ) ?></label>
<input type="date" id="filter-by-start_date" name="start_date" value="<?= esc_attr( $start_date ) ?>" placeholder="<?php esc_attr_e( 'Start date', 'innstats' ) ?>" <?= esc_attr( $date_disabled ) ?> max="<?= esc_attr( $end_date ?: $today ) ?>">
<label for="filter-by-end_date" class="screen-reader-text"><?php esc_html_e( 'Filter by end date', 'innstats' ) ?></label>
<input type="date" id="filter-by-end_date" name="end_date" value="<?= esc_attr( $end_date ) ?>" placeholder="<?php esc_attr_e( 'End date', 'innstats' ) ?>" <?= esc_attr( $date_disabled ) ?> min="<?= esc_attr( $start_date ?: '' ) ?>" max="<?= esc_attr( $today ) ?>">
<button type="submit" class="button"><?php esc_html_e( 'Filter', 'innstats' ) ?></button>
