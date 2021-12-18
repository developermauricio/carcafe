<?php
/**
 * Register Elementor widget.
 *
 * @package awsm-team-pro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register new Elementor Widget.
 *
 * @author: AWSM Innovations
 */
class AWSMTeamElementorWidget extends \Elementor\Widget_Base {
	/**
	 * Get element name.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'awsm-team';
	}

	/**
	 * Retrieve element title.
	 *
	 * @return string
	 */
	public function get_title() {
		return __( 'AWSM Team', 'awsm-team-pro' );
	}

	/**
	 * Retrieve element icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-gallery-grid';
	}

	/**
	 * Retrieve widget categories.
	 *
	 * @return array
	 */
	public function get_categories() {
		return array( 'general' );
	}

	/**
	 * Register controls - Used to add new controls to any element type.
	 *
	 * @return void
	 */
	protected function _register_controls() {
		$awsm_team = new Awsm_Team();
		$this->start_controls_section(
			'setting_section',
			array(
				'label' => __( 'Settings', 'awsm-team-pro' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);
		$this->add_control(
			'awsm_team_id',
			array(
				'type'    => \Elementor\Controls_Manager::SELECT,
				'id'      => 'awsm_elem_team_id',
				'label'   => __( 'Team', 'awsm-team-pro' ),
				'options' => array( '0' => 'Select team' ) + $awsm_team->get_teams(),
				'default' => __( '0', 'awsm-team-pro' ),
				'event'   => 'awsm_elem_team_id',
			)
		);
		$this->end_controls_section();
	}

	/**
	 * Render element - Generates the final HTML on the frontend.
	 *
	 * @return void
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		if ( isset( $settings['awsm_team_id'] ) && ! empty( $settings['awsm_team_id'] ) ) {
			$shortcode  = '[awsmteam';
			$shortcode .= ' id="' . $settings['awsm_team_id'] . '"';
			$shortcode .= ']';
			$url        = get_edit_post_link( $settings['awsm_team_id'], '' );

			echo do_shortcode( $shortcode );
		} else {
			esc_html_e( 'Select a team', 'awsm-team-pro' );
		}
	}
}
