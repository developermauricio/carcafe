<?php
/**
 * Register Team Beaver Module.
 *
 * @package awsm-team-pro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register new Beaver Module.
 *
 * @author: AWSM Innovations
 */
class FLAwsmTeamModule extends FLBuilderModule {
	/**
	 * Constructor function for the module.
	 *
	 * @method __construct
	 */
	public function __construct() {
		parent::__construct(
			array(
				'name'          => __( 'AWSM Team', 'awsm-team-pro' ),
				'description'   => __( 'Awsm team module', 'awsm-team-pro' ),
				'category'      => __( 'AWSM Team Module', 'awsm-team-pro' ),
				'editor_export' => true, // Defaults to true and can be omitted.
				'enabled'       => true, // Defaults to true and can be omitted.
			)
		);
	}

	/**
	 * Handle settings data before it is saved.
	 *
	 * @method update
	 * @param object $settings A settings object that is going to be saved.
	 * @return object
	 */
	public function update( $settings ) {
		return $settings;
	}

	/**
	 * Add additional methods to this class for use in the
	 * other module files such as preview.php, frontend.php
	 * and frontend.css.php.
	 *
	 * @method render_team
	 * @param object $settings A settings object that is going to be saved.
	 * @return string
	 */
	public function render_team( $settings ) {
		$content = '';
		$team_id = intval( $settings->awsm_team_id );
		if ( $team_id ) {
			$content = do_shortcode( '[awsmteam id=' . $team_id . ']' );
		} else {
			if ( ! empty( $settings->awsm_team_id ) && trim( $settings->awsm_team_id ) !== '' ) {
				$content = $settings->awsm_team_id;
			}
		}
		return $content;
	}
}

/**
 * Register the module and its form settings.
 */
FLBuilder::register_module(
	'FLAwsmTeamModule',
	array(
		'general' => array( // Tab.
			'title'    => __( 'General', 'awsm-team-pro' ), // Tab title.
			'sections' => array( // Tab Sections.
				'general' => array( // Section.
					'title'  => __( 'Settings', 'awsm-team-pro' ), // Section Title.
					'fields' => array( // Section Fields.
						'awsm_team_id' => array(
							'type'    => 'select',
							'label'   => __( 'Select team', 'awsm-team-pro' ),
							'default' => '',
							'options' => array( '' => 'Select team' ) + Awsm_Team::get_teams(),
						),
					),
				),
			),
		),
	)
);
