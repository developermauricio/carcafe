<?php
/**
 * Member Details meta.
 *
 * @package awsm-team-pro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<p>
	<label for="awsm-team-designation"><?php esc_html_e( 'Designation', 'awsm-team-pro' ); ?></label>
	<input class="widefat" type="text" name="awsm-team-designation" id="awsm-team-designation" value="<?php echo esc_html( get_post_meta( $post->ID, 'awsm-team-designation', true ) ); ?>"/>
</p>
<p>
	<label for="awsm-team-short-desc"><?php esc_html_e( 'Short Description (In 140 characters or less)', 'awsm-team-pro' ); ?></label>
	<textarea id="awsm-team-short-desc" name="awsm-team-short-desc" class="widefat" type="text" maxlength="140"><?php echo esc_textarea( get_post_meta( $post->ID, 'awsm-team-short-desc', true ) ); ?></textarea>
</p>

<p class="awsm-sorable-table-label"><?php esc_html_e( 'Additional Information (for Email, Telephone, Fax, etc)', 'awsm-team-pro' ); ?></p>
<table id="repeatable-fieldset-one" width="100%" class="awsm-sorable-table">
	<thead>
		<tr>
			<td width="3%"></td>
			<td width="45%"><?php esc_html_e( 'Label', 'awsm-team-pro' ); ?></td>
			<td width="42%"><?php esc_html_e( 'Content', 'awsm-team-pro' ); ?></td>
			<td width="10%"></td>
		</tr>
	</thead>
	<tbody>
		<?php
		if ( $awsm_contact ) :
			foreach ( $awsm_contact as $field ) :
				$repeater_label   = isset( $field['label'] ) ? $field['label'] : '';
				$repeater_content = isset( $field['content'] ) ? $field['content'] : '';
				?>
				<tr>
					<td><span class="dashicons dashicons-move"></span></td>
					<td><input type="text" placeholder="<?php esc_attr_e( 'ex: Email', 'awsm-team-pro' ); ?>" class="widefat" name="awsm-team-label[]"  value="<?php echo esc_attr( $repeater_label ); ?>" /></td>
					<td><input type="text" placeholder="<?php esc_attr_e( 'mail@example.com', 'awsm-team-pro' ); ?>" class="widefat" name="awsm-team-content[]" value="<?php echo esc_attr( $repeater_content ); ?>" /></td>
					<td><a class="button remove-row" href="#"><?php esc_html_e( 'Remove', 'awsm-team-pro' ); ?></a></td>
				</tr>
				<?php
				endforeach;
			else :
				?>
		<tr>
			<td><span class="dashicons dashicons-move"></span></td>
			<td><input type="text" placeholder="<?php esc_attr_e( 'ex: Email', 'awsm-team-pro' ); ?>" class="widefat" name="awsm-team-label[]" value=""/></td>
			<td><input type="text" placeholder="<?php esc_attr_e( 'mail@example.com', 'awsm-team-pro' ); ?>" class="widefat" name="awsm-team-content[]" value=""/></td>
			<td><a class="button remove-row" href="#"><?php esc_html_e( 'Remove', 'awsm-team-pro' ); ?></a></td>
		</tr>	
		<?php endif; ?>
		<tr class="empty-row screen-reader-text">
			<td><span class="dashicons dashicons-move"></span></td>
			<td><input type="text" class="widefat" placeholder="<?php esc_attr_e( 'ex: Email', 'awsm-team-pro' ); ?>"  name="awsm-team-label[]" /></td>
			<td><input type="text" class="widefat" placeholder="<?php esc_attr_e( 'mail@example.com', 'awsm-team-pro' ); ?>" name="awsm-team-content[]" value=""/></td>
			<td><a class="button remove-row" href="#"><?php esc_html_e( 'Remove', 'awsm-team-pro' ); ?></a></td>
		</tr>
	</tbody>
</table>
<p><a class="button awsm-add-row" href="#" data-table="repeatable-fieldset-one"><?php esc_html_e( 'Add row', 'awsm-team-pro' ); ?></a></p>

<p class="awsm-sorable-table-label"><?php esc_html_e( 'Links (Twitter, LinkedIn, etc)', 'awsm-team-pro' ); ?></p>
<table id="repeatable-fieldset-two" width="100%" class="awsm-sorable-table">
	<thead>
		<tr>
			<td width="3%"></td>
			<td width="45%"><?php esc_html_e( 'Icon', 'awsm-team-pro' ); ?></td>
			<td width="42%"><?php esc_html_e( 'Link', 'awsm-team-pro' ); ?></td>
			<td width="10%"></td>
		</tr>
	</thead>
	<tbody>
		<?php
		if ( $awsm_social ) :
			foreach ( $awsm_social as $field ) :
				$social_link = isset( $field['link'] ) ? $field['link'] : '';
				?>
				<tr>
					<td><span class="dashicons dashicons-move"></span></td>
					<td>
						<?php
						$icon = isset( $field['icon'] ) ? $field['icon'] : '';
						$this->selectbuilder( 'awsm-team-icon[]', $socialicons, $icon, __( 'Select icon', 'awsm-team-pro' ), 'widefat awsm-icon-select' );
						?>
					</td>
					<td><input type="text" placeholder="<?php esc_attr_e( 'ex: http://www.twitter.com/awsmin', 'awsm-team-pro' ); ?>" class="widefat" name="awsm-team-link[]" value="<?php echo esc_attr( $social_link ); ?>" /></td>
					<td><a class="button remove-row" href="#"><?php esc_html_e( 'Remove', 'awsm-team-pro' ); ?></a></td>
				</tr>
				<?php
			endforeach;
		else :
			?>
		 
			<tr>
				<td><span class="dashicons dashicons-move"></span></td>
				<td>
					<?php $this->selectbuilder( 'awsm-team-icon[]', $socialicons, '', __( 'Select icon', 'awsm-team-pro' ), 'widefat awsm-icon-select' ); ?>
				</td>
				<td><input type="text" placeholder="<?php esc_attr_e( 'ex: http://www.twitter.com/awsmin', 'awsm-team-pro' ); ?>" class="widefat" name="awsm-team-link[]" value=""/></td>
				<td><a class="button remove-row" href="#"><?php esc_html_e( 'Remove', 'awsm-team-pro' ); ?></a></td>
			</tr>	
		<?php endif; ?>
		<tr class="empty-row screen-reader-text">
			<td><span class="dashicons dashicons-move"></span></td>
			<td>
				<?php $this->selectbuilder( 'awsm-team-icon[]', $socialicons, '', __( 'Select icon', 'awsm-team-pro' ), 'widefat' ); ?>
			</td>
			<td><input type="text" placeholder="<?php esc_attr_e( 'ex: http://www.twitter.com/awsmin', 'awsm-team-pro' ); ?>" class="widefat" name="awsm-team-link[]" value=""/></td>
			<td><a class="button remove-row" href="#"><?php esc_html_e( 'Remove', 'awsm-team-pro' ); ?></a></td>
		</tr>
	</tbody>
</table>
<p><a class="button awsm-add-row" href="#" data-table="repeatable-fieldset-two"><?php esc_html_e( 'Add row', 'awsm-team-pro' ); ?></a></p>
