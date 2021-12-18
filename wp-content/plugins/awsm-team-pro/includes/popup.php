<?php
/**
 * Select Team Popup.
 *
 * @package awsm-team-pro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div id="awsm-block-popup-wrap">
	<div id="awsm-block-popup">
		<button title="<?php esc_html_e( 'Close', 'awsm-team-pro' ); ?>" type="button" class="atp-close">×</button>
		<div id="popup-header" class="atp-popup-header">
			<h1><?php esc_html_e( 'Choose Team', 'awsm-team-pro' ); ?></h1>
		</div>
		<div class="atp-section">
			<div id="awsm-block-message" class="awsm-error" style="display:none;">
				<p></p>
			</div>
			<div class="atp-container">
				<form action="" onSubmit="return false" method="post" >
					<label for="cmb-teams"><?php esc_html_e( 'Select Team', 'awsm-team-pro' ); ?>: </label>
					<?php

					$teams = $this->get_teams();
					$this->selectbuilder( 'cmb-teams', $teams, '', 'Select team', 'awsm-select-default dyn-sel awsm-styles', 'key' );

					?>
				</form>
				<input type="hidden" name="shortcode" id="atp-shortcode">
			</div>
			
		</div>
		<div class="mceActionPanel atp-action-panel">
			<div style="float: right">
				<input type="button" id="awsm-insert-team" name="insert" data-txt="<?php esc_html_e( 'Insert', 'awsm-team-pro' ); ?>" data-loading="<?php esc_html_e( 'Loading...', 'awsm-team-pro' ); ?>" class="atp-btn button button-primary button-medium" value="<?php esc_html_e( 'Add Team', 'awsm-team-pro' ); ?>" disabled />
			</div>
			<div style="float: left">
				<input type="button" name="cancel" class="atp-btn button cancel-awsm-block button-medium" value="<?php esc_html_e( 'Cancel', 'awsm-team-pro' ); ?>" />
			</div>
			<div class="clear"></div>
		</div>
	</div>
</div>
