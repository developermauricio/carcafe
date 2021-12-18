<?php
/**
 * Contact Info Template Part.
 *
 * @package awsm-team-pro
 */

if ( ! empty( $teamdata['awsm_contact'] ) ) {
	echo '<div class="awsm-contact-details">';
	foreach ( $teamdata['awsm_contact'] as $contact ) {
		if ( isset( $contact['content'] ) ) {
			if ( filter_var( $contact['content'], FILTER_VALIDATE_EMAIL ) ) {
				$contact['content'] = sprintf( '<a href="mailto:%1$s">%1$s</a>', esc_attr( $contact['content'] ) );
			} elseif ( $this->validate_phone_number( $contact['content'] ) == true ) {
				$contact['content'] = sprintf( '<a href="tel:%1$s">%1$s</a>', esc_attr( $contact['content'] ) );
			}
			echo '<p><span>' . esc_html( $contact['label'] ) . ':</span>' . wp_kses( $contact['content'], 'post' ) . '</p>';
		}
	}
	echo '</div>';
}

