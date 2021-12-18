<?php
/**
 * Add to team meta.
 *
 * @package awsm-team-pro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="wrap">
	<div class="awsm-team-choose">
		<div class="awsm-team-choose-inner">
			<div class="awsm-member-choose-teams">
				<div class="awsm-heading-group">
					<span><?php esc_html_e( 'Select teams this member belongs from the list or create a new one.', 'awsm-team-pro' ); ?></span>
				</div>
				<div class="awsm-select-teams">
					<?php
					$checked = '';
					echo '<ul id="teamchecklist">';
					if ( $teams ) {
						?>
						<?php
						foreach ( $teams as $key => $team ) {
							if ( in_array( $key, array_keys( $member_teams ) ) ) {
								$checked = ' checked';
							} else {
								$checked = '';
							}
							echo '<li><input type="checkbox" name="awsm-member-teams[]" value="' . esc_attr( $key ) . '" ' . esc_attr( $checked ) . ' /><input type="hidden" value="' . esc_attr( $key ) . '" name="awsm-team-list[]">' . esc_html( $team ) . '</li>';
						}
						?>
						<?php
					}

					echo '</ul>';
					?>
				</div><!-- .awsm-select-teams -->
				<div id="team-adder" class="wp-hidden-children">
					<a id="team-add-toggle" href="#team-add" class="taxonomy-add-new"><?php esc_html_e( '+ Add New Team', 'awsm-team-pro' ); ?></a>
					<p id="team-add" class="wp-hidden-child">
						<label class="screen-reader-text" for="newteam"><?php esc_html_e( '+ Add New Team', 'awsm-team-pro' ); ?></label>
						<input type="text" name="newteam" id="newteam" class="form-required form-input-tip" value="" aria-required="true" />
						<input type="button" id="link-team-add-submit" data-wp-lists="add:teamchecklist:link-team-add" data-member-id="<?php echo esc_attr( $post->ID ); ?>" class="button" value="<?php esc_attr_e( 'Add New Team', 'awsm-team-pro' ); ?>" />
						<?php wp_nonce_field( 'add-link-team', '_ajax_nonce', false ); ?>
						<span id="team-ajax-response"></span>
					</p>
				</div>		
			</div><!-- .awsm-member-choose-teams -->	
		</div><!-- .awsm-team-choose-inner -->
	</div><!-- .awsm-team-choose -->
</div><!-- .wrap -->	
