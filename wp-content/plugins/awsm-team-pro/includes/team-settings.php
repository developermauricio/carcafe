<?php
/**
 * Team Settings.
 *
 * @package awsm-team-pro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$thumbnail_settings = get_option(
	'awsm_team_pro_thumbnail_settings',
	array(
		'enable_crop' => 'enable',
		'width'       => 500,
		'height'      => 500,
	)
);

$license_key = get_option( 'awsm_team_pro_license' );

$deep_linking   = Awsm_Team::get_deep_linking_settings();
$is_deep_linked = $deep_linking['enable'] === 'enable' ? true : false;

settings_errors();
?>
<div id="awsm-team-pro-settings-general" class="wrap awsm-team-pro-settings">
	<h1><?php esc_html_e( 'Team Settings', 'awsm-team-pro' ); ?></h1>

	<form method="POST" action="options.php" id="awsm-team-pro-settings-general-form">
		<?php
		settings_fields( 'awsm-team-pro-license-settings' );
		?>

		<div class="awsm-form-section-main awsm-sub-options-container" id="awsm-team-options-container">
			<table class="form-table">
				<tbody>
				<tr>
					<th scope="row"><?php esc_html_e( 'Team photos', 'awsm-team-pro' ); ?></th>
					<td>
						<fieldset>
							<legend class="screen-reader-text"><span><?php esc_html_e( 'Team photos', 'awsm-team-pro' ); ?></span></legend>
							<label for="awsm_team_pro_enable_crop">
								<input type="checkbox" name="awsm_team_pro_thumbnail_settings[enable_crop]" value="enable" id="awsm_team_pro_enable_crop"<?php checked( $thumbnail_settings['enable_crop'], 'enable', true ); ?>><?php esc_html_e( 'Disable default image cropping', 'awsm-team-pro' ); ?>
							</label>
							<div class="awsm-team-pro-thumbnail-options<?php echo $thumbnail_settings['enable_crop'] && $thumbnail_settings['enable_crop'] === 'enable' ? ' show' : ''; ?>" id="awsm-team-pro-thumbnail-options">
							<h4><?php esc_html_e( 'Enter custom dimensions', 'awsm-team-pro' ); ?></h4>
								<label for="awsm_team_pro_thumbnail_size_w"><?php esc_html_e( 'Width', 'awsm-team-pro' ); ?></label>
								<input name="awsm_team_pro_thumbnail_settings[width]" type="number" step="1" min="0" id="awsm_team_pro_thumbnail_size_w" value="<?php echo esc_attr( $thumbnail_settings['width'] ); ?>" class="small-text awsm-team-size-field" />
								<br />
								<label for="awsm_team_pro_thumbnail_size_h"><?php esc_html_e( 'Height', 'awsm-team-pro' ); ?></label>
								<input name="awsm_team_pro_thumbnail_settings[height]" type="number" step="1" min="0" id="awsm_team_pro_thumbnail_size_h" value="<?php echo esc_attr( $thumbnail_settings['height'] ); ?>" class="small-text awsm-team-size-field" />
								<p>
									<?php
										/* translators: %1$s: Opening anchor tag, %2$s: closing anchor tag */
										printf( esc_html__( 'The change will not affect existing images in the website. If you want the existing images to be cropped in the custom dimensions consider using a plugin like %1$sRegenerate Thumbnails%2$s', 'awsm-team-pro' ), '<a href="https://wordpress.org/plugins/regenerate-thumbnails/" target="_blank">', '</a>' );
									?>
								</p>
							</div>
						</fieldset>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Deep linking', 'awsm-team-pro' ); ?></th>
					<td>
						<fieldset>
							<legend class="screen-reader-text"><span><?php esc_html_e( 'Deep linking', 'awsm-team-pro' ); ?></span></legend>
							<label for="awsm_team_pro_enable_deep_linking">
								<input type="checkbox" name="awsm_team_pro_deep_link_settings[enable]" value="enable" id="awsm_team_pro_enable_deep_linking"<?php checked( $is_deep_linked ); ?>><?php esc_html_e( 'Enable deep linking', 'awsm-team-pro' ); ?>
							</label>
							<div class="awsm-team-pro-deep-linking-options<?php echo $is_deep_linked ? ' show' : ''; ?>" id="awsm-team-pro-deep-linking-options">
								<h4><?php esc_html_e( 'Member', 'awsm-team-pro' ); ?></h4>
									<label for="awsm_team_pro_member_deep_link_prefix"><?php esc_html_e( 'Prefix', 'awsm-team-pro' ); ?></label>
									<input name="awsm_team_pro_deep_link_settings[member][prefix]" type="text" class="awsm-team-req-field awsm-team-deep-link-field" id="awsm_team_pro_member_deep_link_prefix" value="<?php echo esc_attr( $deep_linking['member']['prefix'] ); ?>"<?php echo $is_deep_linked ? ' required' : ''; ?>/>
								<br />
									<label for="awsm_team_pro_member_deep_link_suffix"><?php esc_html_e( 'Suffix', 'awsm-team-pro' ); ?></label>
									<input name="awsm_team_pro_deep_link_settings[member][suffix]" type="text" class="awsm-team-deep-link-field" id="awsm_team_pro_member_deep_link_suffix" value="<?php echo esc_attr( $deep_linking['member']['suffix'] ); ?>" />
								<br />
								<h4><?php esc_html_e( 'Team', 'awsm-team-pro' ); ?></h4>
									<label for="awsm_team_pro_team_deep_link_prefix"><?php esc_html_e( 'Prefix', 'awsm-team-pro' ); ?></label>
									<input name="awsm_team_pro_deep_link_settings[team][prefix]" type="text" class="awsm-team-req-field awsm-team-deep-link-field" id="awsm_team_pro_team_deep_link_prefix" value="<?php echo esc_attr( $deep_linking['team']['prefix'] ); ?>"<?php echo $is_deep_linked ? ' required' : ''; ?>/>
									<br />
									<label for="awsm_team_pro_team_deep_link_suffix"><?php esc_html_e( 'Suffix', 'awsm-team-pro' ); ?></label>
									<input name="awsm_team_pro_deep_link_settings[team][suffix]" type="text" class="awsm-team-deep-link-field" id="awsm_team_pro_team_deep_link_suffix" value="<?php echo esc_attr( $deep_linking['team']['suffix'] ); ?>" />
							</div>
						</fieldset>
					</td>
				</tr>
					<tr>
						<th scope="row">
							<label for="awsm_team_pro_license"><?php esc_html_e( 'Envato Purchase Code', 'awsm-team-pro' ); ?></label>
						</th>
						<td>
							<p>
								<?php esc_html_e( 'Enter purchase code for AWSM Team Pro. This is required for automatic updates and support. ', 'awsm-team-pro' ); ?> <br> <?php esc_html_e( 'NOTE: You need to have a valid', 'awsm-team-pro' ); ?> <strong><?php esc_html_e( 'support license', 'awsm-team-pro' ); ?></strong> <?php esc_html_e( 'for activating automatic updates', 'awsm-team-pro' ); ?>
							</p>
								<div class="awsm-team-pro-inputholder">
									<input type="password" name="awsm_team_pro_license" id="awsm_team_pro_license" value="<?php echo esc_attr( $license_key ); ?>" class="regular-text" />
								</div>
							<p class=" description awsm-team-pro-instructions">
								<a href="https://1.envato.market/getcode" target="_blank"><?php esc_html_e( 'How to get the purchase code', 'awsm-team-pro' ); ?></a>
							</p>
						</td>
					</tr>
				</tbody>
			</table>
		</div><!-- #awsm-team-options-container -->
		<div class="awsm-form-footer">
			<?php submit_button( '', 'primary large' ); ?>
		</div><!-- .awsm-form-footer -->
		<script type="text/html" id="tmpl-awsm-team-pro-settings-error">
			<div class="awsm-team-pro-error-container">
				<div class="awsm-team-pro-error">
					<p>
						<strong>
							<# if( data.isInvalidKey ) { #>
								<?php
									esc_html_e( 'The prefix/suffix should only contain alphanumeric, latin characters separated by hyphen/underscore, and cannot begin or end with a hyphen/underscore.', 'awsm-team-pro' );
								?>
							<# } #>
						</strong>
					</p>
				</div>
			</div>
		</script>
	</form>
</div>
