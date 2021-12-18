<?php
/**
 * Plugin Name: AWSM Team Pro
 * Plugin URI: http://awsm.in/team-pro-documentation
 * Description: The most versatile plugin to create and manage your Team page. Packed with 8 unique presets and number of styles to choose from.
 * Version: 1.8.1
 * Author: AWSM Innovations
 * Author URI: http://awsm.in/
 * License: GPL
 * Copyright: AWSM Innovations
 * Text domain: awsm-team-pro
 * Domain Path: /language
 *
 * @package awsm-team-pro
 */

/**
 * Exit if direct access
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Awsm_Team' ) ) :
	/**
	 * Team main class.
	 *
	 * @author: AWSM Innovations
	 */
	class Awsm_Team {
		/**
		 * The single instance of the class.
		 *
		 * @var Awsm_Team
		 */
		private static $instance = null;

		/**
		 * Kernl API URL.
		 *
		 * @var string
		 */
		public static $kernl_base_url = 'https://kernl.us/api/v1';

		/**
		 * Unique ID for the plugin.
		 *
		 * @var string
		 */
		public static $uuid = '5ce3a2c81f24717dd2f64805';

		/**
		 * Plugin settings.
		 *
		 * @var array
		 */
		public $settings = array();

		/**
		 * Awsm Team Constructor.
		 */
		public function __construct() {
			$this->settings = array(
				'plugin_path'     => plugin_dir_path( __FILE__ ),
				'plugin_url'      => plugin_dir_url( __FILE__ ),
				'plugin_base'     => dirname( plugin_basename( __FILE__ ) ),
				'plugin_base_url' => plugin_basename( __FILE__ ),
				'plugin_file'     => __FILE__,
				'plugin_version'  => '1.8.1',
			);

			$this->run_plugin();
			$this->adminfunctions();
			$this->update_plugin();
		}

		/**
		 * Singleton.
		 *
		 * @return self Main instance.
		 */
		public static function init() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * Localization.
		 */
		public function load_plugin_textdomain() {
			load_plugin_textdomain( 'awsm-team-pro', false, $this->settings['plugin_base'] . '/language/' );
		}

		/**
		 * Main plugin function.
		 *
		 * @since 1.0.0
		 */
		public function run_plugin() {
			add_action( 'plugins_loaded', array( $this, 'load_plugin_textdomain' ) );
			add_action( 'init', array( $this, 'create_member_support' ) );
			add_action( 'init', array( $this, 'create_filter_taxonomy' ) );
			add_action( 'init', array( $this, 'custom_image_size' ) );
			add_action( 'init', array( $this, 'vc_support' ) );
			add_action( 'init', array( $this, 'load_modules' ) );
			add_action( 'elementor/widgets/widgets_registered', array( $this, 'elementor_support' ) );
			add_shortcode( 'awsmteam', array( $this, 'awsmteam_shortcode' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'embed_front_script_styles' ) );
			add_action( 'wp_head', array( $this, 'custom_css' ) );
			add_filter( 'widget_text', array( $this, 'widget_text_filter' ), 9 );
			add_action( 'after_setup_theme', array( $this, 'awsm_add_thumbnail_support' ) );
			add_filter( 'post_thumbnail_size', array( $this, 'awsm_remove_srcset' ) );
			add_action( 'init', array( $this, 'awsm_team_blocks' ) );
			add_action( 'after_plugin_row_' . $this->settings['plugin_base_url'], array( $this, 'after_plugin_row' ), 100 );
			add_action( 'admin_init', array( $this, 'register_team_settings' ) );
			add_action( 'admin_notices', array( $this, 'automatic_update_notice' ) );
			add_action( 'wp_ajax_awsm_team_pro_admin_notice', array( $this, 'awsm_team_pro_admin_notice' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts_global' ) );
		}

		/**
		 * Visual composer support.
		 *
		 * @since 1.0.0
		 */
		public function vc_support() {
			if ( ! defined( 'WPB_VC_VERSION' ) || ! function_exists( 'vc_map' ) ) {
				return;
			}
			if ( function_exists( 'vc_map' ) ) {
				vc_map(
					array(
						'name'        => __( 'Awsm Team', 'awsm-team-pro' ),
						'description' => __( 'Awsm Team', 'awsm-team-pro' ),
						'base'        => 'awsmteam',
						'controls'    => 'full',
						'icon'        => esc_url( plugins_url( 'images/team-vc-icon.png', __FILE__ ) ),
						'category'    => __( 'Content', 'js_composer' ), // phpcs:ignore WordPress.WP.I18n.TextDomainMismatch
						'params'      => array(
							array(
								'type'       => 'dropdown',
								'holder'     => 'div',
								'class'      => '',
								'heading'    => __( 'Select Team', 'awsm-team-pro' ),
								'param_name' => 'id',
								'value'      => $this->vc_get_teams(),
							),
						),
					)
				);
			}
		}

		/**
		 * Get team array.
		 *
		 * @since 1.0.0
		 * @return array Team associative array
		 */
		public function get_teams() {
			$args     = array(
				'post_type'      => 'awsm_team',
				'posts_per_page' => -1,
			);
			$teams    = new WP_Query( $args );
			$teamlist = array();
			if ( isset( $teams->posts ) && ! empty( $teams->posts ) ) {
				$teamlist = wp_list_pluck( $teams->posts, 'post_title', 'ID' );
			}
			return $teamlist;
		}

		/**
		 * Get members array.
		 *
		 * @since 1.0.0
		 * @return array Members associative array
		 */
		public function get_members() {
			$memberslist = array();
			$args        = array(
				'post_type'      => 'awsm_team_member',
				'posts_per_page' => -1,
			);
			$members     = new WP_Query( $args );
			$memberslist = wp_list_pluck( $members->posts, 'post_title', 'ID' );
			return $memberslist;
		}

		/**
		 * Get team array of a member.
		 *
		 * @since 1.0.0
		 * @param int $member_id The Member ID.
		 * @return array Team associative array
		 */
		public function get_teams_of_member( $member_id ) {
			$teamlist            = array();
			$teams_of_member_arr = array();
			if ( metadata_exists( 'post', $member_id, 'awsm-member-teams' ) ) {
				$member_teams = get_post_meta( $member_id, 'awsm-member-teams', true );
				if ( ! empty( $member_teams ) ) {
					foreach ( explode( ',', $member_teams ) as $team ) {
						if ( $team ) {
							$teamlist[ $team ] = get_the_title( $team );
						}
					}
				}
			} else {
				$allteams = $this->get_teams();
				foreach ( $allteams as $key => $team_id ) {
					$teams_of_member = get_post_meta( $key, 'memberlist', true );
					if ( $teams_of_member ) {
						if ( get_post_meta( $key, 'memberlist', false ) ) {
							$teams_of_member_arr = $this->array_flatten( get_post_meta( $key, 'memberlist', false ) );
						}
						if ( in_array( $member_id, $teams_of_member_arr ) ) {
							$teamlist[ $key ] = get_the_title( $key );
						}
					}
				}
			}
			return $teamlist;

		}

		/**
		 * Team custom css on theme head.
		 *
		 * @since 1.0.0
		 */
		public function custom_css() {
			global $wp_query;
			$posts      = $wp_query->posts;
			$snippet    = '';
			$shortcodes = array();
			if ( $posts ) {
				foreach ( $posts as $post ) {
					$shortcodes = $this->get_all_attributes( 'awsmteam', $post->post_content );
					if ( ! empty( $shortcodes ) ) {
						foreach ( $shortcodes as $shortcode ) {
							if ( isset( $shortcode['id'] ) ) {
								$custom_css = get_post_meta( $shortcode['id'], 'custom_css', true );
								if ( $custom_css ) {
									$snippet .= $custom_css;
								}
							}
						}
					}
				}
				if ( $snippet ) {
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					printf( '<!-- AWSM team pro custom CSS - begin  --><style type="text/css">%s</style><!-- AWSM team pro custom CSS - end  -->', $snippet );
				}
			}
		}

		/**
		 * Custom image size for team memebers.
		 *
		 * @since 1.0.0
		 */
		public function custom_image_size() {
			$thumbnail_settings = get_option(
				'awsm_team_pro_thumbnail_settings',
				array(
					'enable_crop' => 'enable',
					'width'       => 500,
					'height'      => 500,
				)
			);

			if ( $thumbnail_settings['enable_crop'] === 'enable' ) {
				if ( function_exists( 'add_image_size' ) ) {
					add_image_size( 'awsm_team', $thumbnail_settings['width'], $thumbnail_settings['height'], true );
				}
			}
		}

		/**
		 * AWSM team shortocde.
		 *
		 * @since 1.0.0
		 * @param array $atts An associative array of attributes.
		 * @return string
		 */
		public function awsmteam_shortcode( $atts ) {
			$settings = shortcode_atts(
				array(
					'id'       => false,
					'order_by' => false,
					'order'    => false,
					'preset'   => false,
				),
				$atts
			);

			$id      = $settings['id'];
			$orderby = $settings['order_by'];
			$order   = $settings['order'];

			$options = $this->get_options( 'awsm_team', $id );
			if ( ! $options ) {
				return '<div class="awsm-team-error">' . __( 'Team not found', 'awsm-team-pro' ) . '</div>';
			}
			if ( empty( $options['memberlist'] ) ) {
				return '<div class="awsm-team-error">' . __( 'No members found', 'awsm-team-pro' ) . '</div>';
			}

			$content  = '';
			$template = $this->get_template_path( $options['team-style'] . '.php' );
			if ( file_exists( $template ) ) {
				ob_start();
				if ( 'drag-and-drop' === $options['awsm_member_order_by'] ) {
					$teamargs = array(
						'post_type'      => 'awsm_team_member',
						'post__in'       => $options['memberlist'],
						'posts_per_page' => -1,
						'orderby'        => 'post__in',
					);
				} else {
					$teamargs = array(
						'post_type'      => 'awsm_team_member',
						'post__in'       => $options['memberlist'],
						'posts_per_page' => -1,
						'orderby'        => ( $orderby ) ? $orderby : $options['awsm_member_order_by'],
						'order'          => ( $order ) ? $order : $options['awsm_member_order'],
					);
				}
				$team = new WP_Query( $teamargs );
				include $template;
				wp_reset_postdata();
				$content = ob_get_clean();
			}
			return $content;
		}

		/**
		 * Get ordered team filters.
		 *
		 * @param mixed $current_filters Unordered filters.
		 * @return array
		 */
		public function get_ordered_team_filters( $current_filters ) {
			$filters_set      = is_array( $current_filters );
			$filters          = get_terms(
				array(
					'taxonomy'   => 'awsm_team_filters',
					'hide_empty' => false,
				)
			);
			$custom_terms_arr = $filters_set ? array_flip( $current_filters ) : array();
			foreach ( $filters as $filter ) {
				$term_id                      = $filter->term_id;
				$custom_terms_arr[ $term_id ] = array(
					'name'     => $filter->name,
					'disabled' => $filter->count > 0 ? false : true,
					'selected' => $filters_set && in_array( $term_id, $current_filters ),
				);
			}
			return $custom_terms_arr;
		}

		/**
		 * AWSM team filter.
		 *
		 * @since 1.0.0
		 * @param object $team The team object.
		 * @param int    $id The team id.
		 * @return string
		 */
		public function show_team_filter( $team, $id ) {
			$filter_output = '';
			if ( get_post_meta( $id, 'enable_filter', true ) && get_post_meta( $id, 'enable_filter', true ) == 1 ) {
				$filter_array   = array();
				$filter_output .= '<div class="awsm-team-filter-wrap">';
				$filter_output .= '<span class="awsm-filter-btn awsm-active-filter" data-rel="awsm-all" data-info="#all">' . __( 'All', 'awsm-team-pro' ) . '</span>';
				if ( $team->have_posts() ) :
					while ( $team->have_posts() ) :
						$team->the_post();
							$current_filters = get_post_meta( $id, 'team_filters', true );
						if ( ! empty( $current_filters ) ) {
							foreach ( $current_filters as  $filter ) {
								$term = get_term_by( 'id', $filter, 'awsm_team_filters' );
								if ( ! empty( $term ) ) {
									$members_array = get_posts(
										array(
											'posts_per_page' => -1,
											'post_type' => 'awsm_team_member',
											'tax_query' => array(
												array(
													'taxonomy' => 'awsm_team_filters',
													'field'    => 'term_id',
													'terms'    => $term->term_id,
												),
											),
										)
									);
									$members_array = wp_list_pluck( $members_array, 'ID' );
									if ( ( $term->count > 0 ) && in_array( $team->post->ID, get_post_meta( $id, 'memberlist', true ) ) && in_array( $team->post->ID, $members_array ) ) {
										$filter_array[ $term->name ] = $term->term_id;
									}
								}
							}
						}
					endwhile;
				endif;
				if ( ! empty( $filter_array ) ) {
					$current_filters  = get_post_meta( intval( $id ), 'team_filters', true );
					$custom_terms_arr = $this->get_ordered_team_filters( $current_filters );

					if ( ! empty( $custom_terms_arr ) ) {
						foreach ( $custom_terms_arr as $term_id => $term_details ) {
							if ( $term_details['selected'] === true ) {
								$info_data_attr = self::deep_link_attr( $term_id, false, false );
								$filter_output .= '<span data-rel="' . esc_attr( 'awsm-' . str_replace( ' ', '-', $term_id ) ) . '" class="awsm-filter-btn"' . $info_data_attr . '>' . esc_html( $term_details['name'] ) . '</span>';
							}
						}
					}
				}
				$filter_output .= '</div>';
				wp_reset_postdata();
			}
			return $filter_output;
		}

		/**
		 * Ensure post thumbnail support is turned on.
		 */
		public function awsm_add_thumbnail_support() {
			if ( ! current_theme_supports( 'post-thumbnails' ) ) {
				add_theme_support( 'post-thumbnails' );
			}
			add_post_type_support( 'awsm_team_member', 'thumbnail' );
		}

		/**
		 * Get team array for VC support.
		 *
		 * @since 1.0.0
		 * @return array Team associative array
		 */
		public function vc_get_teams() {
			$teamlist = array();
			$args     = array(
				'post_type'      => 'awsm_team',
				'posts_per_page' => -1,
				'post_status'    => 'any',
			);
			$teams    = get_posts( $args );
			if ( $teams ) {
				foreach ( $teams as $team ) {
					$team_title              = ! empty( $team->post_title ) ? $team->post_title . ' ( ID: ' . $team->ID . ')' : "Team ( ID: $team->ID)";
					$teamlist[ $team_title ] = $team->ID;
				}
			} else {
				$teamlist = array( __( 'No Teams', 'awsm-team-pro' ) );
			}
			return $teamlist;
		}

		/**
		 * Register front-end scripts.
		 *
		 * @since 1.0.0
		 */
		public function embed_front_script_styles() {
			wp_register_style( 'awsm-team-css', plugins_url( 'css/team.min.css', $this->settings['plugin_file'] ), false, $this->settings['plugin_version'], 'all' );
			wp_enqueue_style( 'awsm-team-css' );

			wp_register_script( 'awsm-team', plugins_url( 'js/team.min.js', $this->settings['plugin_file'] ), array( 'jquery' ), $this->settings['plugin_version'], true );
			wp_enqueue_script( 'awsm-team' );
			wp_localize_script(
				'awsm-team',
				'awsmTeamPublic',
				array(
					'ajaxurl'      => admin_url( 'admin-ajax.php' ),
					'deep_linking' => self::get_deep_linking_settings(),
				)
			);
		}

		/**
		 *  Create custom post types.
		 *
		 *  @since 1.0.0
		 */
		public function create_member_support() {
			// Create awsm_team_member post type.
			if ( post_type_exists( 'awsm_team_member' ) ) {
				return;
			}
			$singular = __( 'Team Member', 'awsm-team-pro' );
			$plural   = __( 'Team Members', 'awsm-team-pro' );
			$labels   = array(
				'name'                     => $plural,
				'singular_name'            => $singular,
				'menu_name'                => __( 'AWSM Team', 'awsm-team-pro' ),
				'add_new'                  => __( 'Add New Member', 'awsm-team-pro' ),
				/* translators: %s: singular term */
				'add_new_item'             => sprintf( __( 'Add %s', 'awsm-team-pro' ), $singular ),
				/* translators: %s: singular term */
				'new_item'                 => sprintf( __( 'New %s', 'awsm-team-pro' ), $singular ),
				/* translators: %s: singular term */
				'edit_item'                => sprintf( __( 'Edit %s', 'awsm-team-pro' ), $singular ),
				/* translators: %s: singular term */
				'view_item'                => sprintf( __( 'View %s', 'awsm-team-pro' ), $singular ),
				/* translators: %s: singular term */
				'all_items'                => sprintf( __( 'Members', 'awsm-team-pro' ) ),
				/* translators: %s: plural term */
				'search_items'             => sprintf( __( 'Search %s', 'awsm-team-pro' ), $plural ),
				/* translators: %s: plural term */
				'not_found'                => sprintf( __( 'No %s found', 'awsm-team-pro' ), $plural ),
				/* translators: %s: plural term */
				'not_found_in_trash'       => sprintf( __( 'No %s found in trash', 'awsm-team-pro' ), $plural ),
				/* translators: %s: plural term */
				'view_items'               => sprintf( __( 'View %s', 'awsm-team-pro' ), $plural ),
				/* translators: %s: singular term */
				'item_published'           => sprintf( __( '%s published.', 'awsm-team-pro' ), $singular ),
				/* translators: %s: singular term */
				'item_published_privately' => sprintf( __( '%s published privately.', 'awsm-team-pro' ), $singular ),
				/* translators: %s: singular term */
				'item_reverted_to_draft'   => sprintf( __( '%s reverted to draft.', 'awsm-team-pro' ), $singular ),
				/* translators: %s: singular term */
				'item_scheduled'           => sprintf( __( '%s scheduled.', 'awsm-team-pro' ), $singular ),
				/* translators: %s: singular term */
				'item_updated'             => sprintf( __( '%s updated.', 'awsm-team-pro' ), $singular ),
			);
			$cp_args  = array(
				'labels'          => $labels,
				/* translators: %s: plural term */
				'description'     => sprintf( __( 'This is where you can create and manage %s.', 'awsm-team-pro' ), $plural ),
				'public'          => false,
				'show_ui'         => true,
				'show_in_menu'    => true,
				'capability_type' => 'post',
				'supports'        => array(
					'title',
					'editor',
					'thumbnail',
					'custom-fields',
				),
				'menu_icon'       => 'dashicons-admin-users',
				'show_in_rest'    => true,
			);
			register_post_type( 'awsm_team_member', $cp_args );

			if ( post_type_exists( 'awsm_team' ) ) {
				return;
			}
			$singular = __( 'Team', 'awsm-team-pro' );
			$plural   = __( 'Teams', 'awsm-team-pro' );
			$labels   = array(
				'name'                     => $plural,
				'singular_name'            => $singular,
				'menu_name'                => __( 'Awsm Team', 'awsm-team-pro' ),
				'add_new'                  => __( 'Add Team', 'awsm-team-pro' ),
				/* translators: %s: singular term */
				'add_new_item'             => sprintf( __( 'Add %s', 'awsm-team-pro' ), $singular ),
				/* translators: %s: singular term */
				'new_item'                 => sprintf( __( 'New %s', 'awsm-team-pro' ), $singular ),
				/* translators: %s: singular term */
				'edit_item'                => sprintf( __( 'Edit %s', 'awsm-team-pro' ), $singular ),
				/* translators: %s: singular term */
				'view_item'                => sprintf( __( 'View %s', 'awsm-team-pro' ), $singular ),
				'all_items'                => sprintf( __( 'Teams', 'awsm-team-pro' ) ),
				/* translators: %s: plural term */
				'search_items'             => sprintf( __( 'Search %s', 'awsm-team-pro' ), $plural ),
				/* translators: %s: plural term */
				'not_found'                => sprintf( __( 'No %s found', 'awsm-team-pro' ), $plural ),
				/* translators: %s: plural term */
				'not_found_in_trash'       => sprintf( __( 'No %s found in trash', 'awsm-team-pro' ), $plural ),
				/* translators: %s: plural term */
				'view_items'               => sprintf( __( 'View %s', 'awsm-team-pro' ), $plural ),
				/* translators: %s: singular term */
				'item_published'           => sprintf( __( '%s published.', 'awsm-team-pro' ), $singular ),
				/* translators: %s: singular term */
				'item_published_privately' => sprintf( __( '%s published privately.', 'awsm-team-pro' ), $singular ),
				/* translators: %s: singular term */
				'item_reverted_to_draft'   => sprintf( __( '%s reverted to draft.', 'awsm-team-pro' ), $singular ),
				/* translators: %s: singular term */
				'item_scheduled'           => sprintf( __( '%s scheduled.', 'awsm-team-pro' ), $singular ),
				/* translators: %s: singular term */
				'item_updated'             => sprintf( __( '%s updated.', 'awsm-team-pro' ), $singular ),
			);
			$cp_args  = array(
				'labels'          => $labels,
				/* translators: %s: plural term */
				'description'     => sprintf( __( 'This is where you can create and manage %s.', 'awsm-team-pro' ), $plural ),
				'show_ui'         => true,
				'show_in_menu'    => 'edit.php?post_type=awsm_team_member',
				'capability_type' => 'post',
				'supports'        => array(
					'title',
				),
				'taxonomies'      => array(
					'awsm_team_filters',
				),
			);
			register_post_type( 'awsm_team', $cp_args );
		}

		/**
		 * Get the post updated messages.
		 *
		 * @param array   $singular_label Post singular label.
		 * @param WP_Post $post Post object.
		 * @return array
		 */
		public function get_post_updated_messages( $singular_label, $post ) {
			$scheduled_date = date_i18n( __( 'M j, Y @ H:i', 'default' ), strtotime( $post->post_date ) );

			return array(
				0  => '', // Unused. Messages start at index 1.
				/* translators: %s: singular term */
				1  => sprintf( __( '%s updated.', 'awsm-team-pro' ), $singular_label ),
				2  => __( 'Custom field updated.', 'default' ),
				3  => __( 'Custom field deleted.', 'default' ),
				/* translators: %s: singular term */
				4  => sprintf( __( '%s updated.', 'awsm-team-pro' ), $singular_label ),
				/* translators: %1$s: singular term, %2$s: date and time of the revision */
				5  => isset( $_GET['revision'] ) ? sprintf( __( '%1$s restored to revision from %2$s.', 'awsm-team-pro' ), $singular_label, wp_post_revision_title( intval( $_GET['revision'] ), false ) ) : false,
				/* translators: %s: singular term */
				6  => sprintf( __( '%s published.', 'awsm-team-pro' ), $singular_label ),
				/* translators: %s: singular term */
				7  => sprintf( __( '%s saved.', 'awsm-team-pro' ), $singular_label ),
				/* translators: %s: singular term */
				8  => sprintf( __( '%s submitted.', 'awsm-team-pro' ), $singular_label ),
				/* translators: %1$s: singular term, %2$s: scheduled date */
				9  => sprintf( __( '%1$s scheduled for: %2$s.', 'awsm-team-pro' ), $singular_label, '<strong>' . $scheduled_date . '</strong>' ),
				/* translators: %s: singular term */
				10 => sprintf( __( '%s draft updated.', 'awsm-team-pro' ), $singular_label ),
			);
		}

		/**
		 * Customize the post updated messages.
		 *
		 * @param array $messages Post updated messages.
		 * @return array
		 */
		public function post_updated_messages( $messages ) {
			global $post, $post_ID;
			$messages['awsm_team_member'] = $this->get_post_updated_messages( __( 'Team Member', 'awsm-team-pro' ), $post );
			$messages['awsm_team']        = $this->get_post_updated_messages( __( 'Team', 'awsm-team-pro' ), $post );
			return $messages;
		}

		/**
		 * Customize the posts bulk action updated messages.
		 *
		 * @param array $bulk_messages Arrays of messages, each keyed by the corresponding post type.
		 * @param array $bulk_counts Array of item counts for each message, used to build internationalized strings.
		 * @return array
		 */
		public function posts_bulk_updated_messages( $bulk_messages, $bulk_counts ) {
			$bulk_messages['awsm_team_member'] = array(
				/* translators: %s: member count */
				'updated'   => _n( '%s team member updated.', '%s team members updated.', $bulk_counts['updated'], 'awsm-team-pro' ),
				/* translators: %s: member count */
				'locked'    => _n( '%s team member not updated, somebody is editing it.', '%s team members not updated, somebody is editing them.', $bulk_counts['locked'], 'awsm-team-pro' ),
				/* translators: %s: member count */
				'deleted'   => _n( '%s team member permanently deleted.', '%s team members permanently deleted.', $bulk_counts['deleted'], 'awsm-team-pro' ),
				/* translators: %s: member count */
				'trashed'   => _n( '%s team member moved to the Trash.', '%s team members moved to the Trash.', $bulk_counts['trashed'], 'awsm-team-pro' ),
				/* translators: %s: member count */
				'untrashed' => _n( '%s team member restored from the Trash.', '%s team members restored from the Trash.', $bulk_counts['untrashed'], 'awsm-team-pro' ),
			);

			$bulk_messages['awsm_team'] = array(
				/* translators: %s: team count */
				'updated'   => _n( '%s team updated.', '%s teams updated.', $bulk_counts['updated'], 'awsm-team-pro' ),
				/* translators: %s: team count */
				'locked'    => _n( '%s team not updated, somebody is editing it.', '%s teams not updated, somebody is editing them.', $bulk_counts['locked'], 'awsm-team-pro' ),
				/* translators: %s: team count */
				'deleted'   => _n( '%s team permanently deleted.', '%s teams permanently deleted.', $bulk_counts['deleted'], 'awsm-team-pro' ),
				/* translators: %s: team count */
				'trashed'   => _n( '%s team moved to the Trash.', '%s teams moved to the Trash.', $bulk_counts['trashed'], 'awsm-team-pro' ),
				/* translators: %s: team count */
				'untrashed' => _n( '%s team restored from the Trash.', '%s teams restored from the Trash.', $bulk_counts['untrashed'], 'awsm-team-pro' ),
			);
			return $bulk_messages;
		}

		/**
		 *  Create custom taxonomy for filters.
		 *
		 *  @since 1.0.0
		 */
		public function create_filter_taxonomy() {
			$labels = array(
				'name'                       => _x( 'Filters', 'taxonomy general name', 'awsm-team-pro' ),
				'singular_name'              => _x( 'Filter', 'taxonomy singular name', 'awsm-team-pro' ),
				'search_items'               => __( 'Search Filters', 'awsm-team-pro' ),
				'popular_items'              => __( 'Popular Filters', 'awsm-team-pro' ),
				'all_items'                  => __( 'All Filters', 'awsm-team-pro' ),
				'parent_item'                => null,
				'parent_item_colon'          => null,
				'edit_item'                  => __( 'Edit Filter', 'awsm-team-pro' ),
				'update_item'                => __( 'Update Filter', 'awsm-team-pro' ),
				'add_new_item'               => __( 'Add New Filter', 'awsm-team-pro' ),
				'new_item_name'              => __( 'New Filter Name', 'awsm-team-pro' ),
				'separate_items_with_commas' => __( 'Separate Filters with commas', 'awsm-team-pro' ),
				'add_or_remove_items'        => __( 'Add or remove Filters', 'awsm-team-pro' ),
				'choose_from_most_used'      => __( 'Choose from the most used Filters', 'awsm-team-pro' ),
				'not_found'                  => __( 'No Filters found.', 'awsm-team-pro' ),
				'menu_name'                  => __( 'Filters', 'awsm-team-pro' ),
			);
			register_taxonomy(
				'awsm_team_filters',
				'awsm_team_member',
				array(
					'labels'             => $labels,
					'public'             => true,
					'publicly_queryable' => false,
					'show_ui'            => true,
					'hierarchical'       => true,
					// added this for gutterberg compatibility.
					'show_in_rest'       => true,
					'show_in_menu'       => 'edit.php?post_type=awsm_team_member',
				)
			);
		}

		/**
		 * Initiate admin functions.
		 *
		 * @since 1.0.0
		 */
		public function adminfunctions() {
			if ( is_admin() ) {
				add_action( 'add_meta_boxes', array( $this, 'register_metaboxes' ), 1 );
				add_action( 'save_post', array( $this, 'save_metabox_data' ), 10, 3 );
				add_action( 'admin_enqueue_scripts', array( $this, 'meta_box_scripts' ), 10, 1 );
				add_action( 'admin_enqueue_scripts', array( $this, 'embed_front_script_styles' ) );
				add_action( 'admin_menu', array( $this, 'add_submenu_items' ), 12 );
				add_action( 'edit_form_after_title', array( $this, 'shortcode_preview' ) );
				add_filter( 'manage_awsm_team_member_posts_columns', array( $this, 'custom_columns_member' ) );
				add_action( 'manage_awsm_team_member_posts_custom_column', array( $this, 'custom_columns_member_data' ), 10, 2 );
				add_filter( 'manage_awsm_team_posts_columns', array( $this, 'custom_columns_team' ) );
				add_action( 'manage_awsm_team_posts_custom_column', array( $this, 'custom_columns_team_data' ), 10, 2 );
				add_filter( 'admin_post_thumbnail_html', array( $this, 'image_help' ) );
				add_filter( 'admin_post_thumbnail_size', array( $this, 'custom_admin_thumb_size' ) );
				add_action( 'wp_ajax_awsm_create_team', array( $this, 'awsm_create_team' ) );
				add_action( 'wp_ajax_update_member_dropdown', array( $this, 'update_member_dropdown' ) );
				add_filter( 'manage_taxonomies_for_awsm_team_member_columns', array( $this, 'awsm_team_member_columns' ), 11, 1 );
				add_action( 'wp_enqueue_media', array( $this, 'team_helper' ) );
				add_action( 'admin_footer', array( $this, 'choose_team' ) );
				add_filter( 'puc_manual_check_link-awsm-team-pro', '__return_false' );
				add_filter( 'post_updated_messages', array( $this, 'post_updated_messages' ) );
				add_filter( 'bulk_post_updated_messages', array( $this, 'posts_bulk_updated_messages' ), 10, 2 );
			}
		}

		/**
		 * Custom thumbnail size for awsm_team_member.
		 *
		 * @since 1.0.0
		 * @param string|array $thumb_size Post thumbnail image size to display in the meta box.
		 * @return string|array
		 */
		public function custom_admin_thumb_size( $thumb_size ) {
			global $post_type, $post;
			if ( 'awsm_team_member' === $post_type ) {
				$thumb_size = 'awsm_team';
			}
			return $thumb_size;
		}

		/**
		 * Image size help text.
		 *
		 * @since 1.0.0
		 * @param string $content Admin post thumbnail HTML markup.
		 * @return string
		 */
		public function image_help( $content ) {
			global $post_type, $post;
			if ( 'awsm_team_member' === $post_type ) {
				if ( ! has_post_thumbnail( $post->ID ) ) {
					$content .= '<p>' . __( 'Please upload square-cropped photos with a minimum dimension of 500px', 'awsm-team-pro' ) . '</p>';
				}
			}
			return $content;
		}

		/**
		 * Custom column on member table.
		 *
		 * @since 1.0.0
		 * @param array $columns An associative array of column headings.
		 * @return array
		 */
		public function custom_columns_member( $columns ) {
			$columns = array(
				'cb'                         => '<input type="checkbox" />',
				'title'                      => __( 'Name', 'awsm-team-pro' ),
				'member_image'               => __( 'Photo', 'awsm-team-pro' ),
				'designation'                => __( 'Designation', 'awsm-team-pro' ),
				'team'                       => __( 'Team', 'awsm-team-pro' ),
				'taxonomy-awsm_team_filters' => __( 'Filters', 'awsm-team-pro' ),
				'date'                       => 'Date',
			);
			return $columns;
		}

		/**
		 * Custom member table data.
		 *
		 * @since 1.0.0
		 * @param string $column The name of the column to display.
		 * @param int    $post_ID The current post ID.
		 */
		public function custom_columns_member_data( $column, $post_ID ) {
			$options      = $this->get_options( 'awsm_team_member', $post_ID );
			$member_teams = $this->get_teams_of_member( $post_ID );
			$team_links   = '';
			foreach ( $member_teams  as $key => $team ) {
				$team_title = get_the_title( $key );
				$title      = ! empty( $team_title ) ? esc_html( $team_title ) : "Team $key";
				if ( false != get_post_status( $key ) && 'trash' != get_post_status( $key ) ) {
					$team_links .= '<a href="' . esc_url( get_edit_post_link( $key ) ) . '" title="' . esc_html( get_the_title( $key ) ) . '">' . $title . '</a>,';
				}
			}
			switch ( $column ) {
				case 'member_image':
					the_post_thumbnail( 'thumbnail' );
					break;
				case 'designation':
					echo esc_html( $options['awsm-team-designation'] );
					break;
				case 'team':
					echo wp_kses(
						rtrim(
							$team_links,
							','
						),
						array(
							'a' => array(
								'href'  => array(),
								'title' => array(),
							),
						)
					);
					break;
			}
		}

		/**
		 * Custom filter column for member.
		 *
		 * @since 1.0.0
		 * @param array $taxonomies Array of taxonomy names to show columns for.
		 * @return array
		 */
		public function awsm_team_member_columns( $taxonomies ) {
			$taxonomies[] = 'awsm_team_filters';
			return $taxonomies;
		}

		/**
		 * Custom member column for team.
		 *
		 * @since 1.0.0
		 * @param array $columns An associative array of column headings.
		 * @return array
		 */
		public function custom_columns_team( $columns ) {
			$columns = array(
				'cb'        => '<input type="checkbox" />',
				'title'     => __( 'Name', 'awsm-team-pro' ),
				'members'   => __( 'Members', 'awsm-team-pro' ),
				'preset'    => __( 'Preset', 'awsm-team-pro' ),
				'style'     => __( 'Style', 'awsm-team-pro' ),
				'shortcode' => __( 'Shortcode', 'awsm-team-pro' ),
			);
			return $columns;
		}

		/**
		 * Custom member column data for team.
		 *
		 * @since 1.0.0
		 * @param string $column The name of the column to display.
		 * @param int    $post_ID The current post ID.
		 */
		public function custom_columns_team_data( $column, $post_ID ) {
			$options = $this->get_options( 'awsm_team', $post_ID );
			$post    = get_post( $post_ID );
			switch ( $column ) {
				case 'members':
					echo esc_html( number_format_i18n( count( $options['memberlist'] ) ) );
					break;
				case 'preset':
					echo esc_html( $options['team-style'] );
					break;
				case 'style':
					echo esc_html( $options['preset'] );
					break;
				case 'shortcode':
					printf( '<code>[awsmteam id="%s"]</code>', esc_html( $post_ID ) );
					break;
			}
		}

		/**
		 * Shortcode preview on team edit page.
		 *
		 * @since 1.0.0
		 * @param WP_Post $post Post object.
		 */
		public function shortcode_preview( $post ) {
			if ( 'awsm_team' == $post->post_type && 'publish' == $post->post_status ) {
				printf( '<p>%1$s: <code>[awsmteam id="%2$s"]</code><button id="copy-awsm" type="button" data-clipboard-text="[awsmteam id=&quot;%2$s&quot;]" class="button">%3$s</button></p>', esc_html__( 'Shortcode', 'awsm-team-pro' ), esc_attr( $post->ID ), esc_html__( 'Copy', 'awsm-team-pro' ) );
			}
		}

		/**
		 * Loads meta box helper scripts.
		 *
		 * @since 1.0.0
		 * @param string $hook The current admin page.
		 */
		public function meta_box_scripts( $hook ) {
			 global $post;
			if ( $hook == 'post-new.php' || $hook == 'post.php' ) {
				if ( 'awsm_team_member' == $post->post_type || 'awsm_team' == $post->post_type ) {
					wp_enqueue_style( 'awsm-team-admin', plugins_url( 'css/admin.css', $this->settings['plugin_file'] ), false, $this->settings['plugin_version'], 'all' );
					wp_enqueue_script( 'team-meta-box', plugins_url( 'js/team-admin.js', $this->settings['plugin_file'] ), array( 'jquery', 'jquery-ui-sortable', 'wp-util' ), $this->settings['plugin_version'] );
					wp_localize_script(
						'team-meta-box',
						'awsm_ajax',
						array(
							'ajax_nonce' => wp_create_nonce( 'ajax_nonce' ),
							'i18n'       => array(
								'copied_message'           => __( 'Copied', 'awsm-team-pro' ),
								'copy_message'             => __( 'Copy', 'awsm-team-pro' ),
								'copy_instruction_message' => __( 'Press "Ctrl + C" to copy', 'awsm-team-pro' ),
								'team_error_message'       => __( 'Oops! something wrong happened', 'awsm-team-pro' ),
								'selectFilter'             => esc_html__( 'Select filters', 'awsm-team-pro' ),
							),
						)
					);
					wp_enqueue_script( 'select2', plugins_url( 'js/select2.min.js', $this->settings['plugin_file'] ), array( 'jquery' ), $this->settings['plugin_version'] );
					wp_enqueue_style( 'select2', plugins_url( 'css/select2.min.css', $this->settings['plugin_file'] ), false, $this->settings['plugin_version'], 'all' );
					wp_enqueue_style( 'awsm-team-icomoon-css', plugins_url( 'css/icomoon.css', $this->settings['plugin_file'] ), false, $this->settings['plugin_version'], 'all' );
				}
			}
		}

		/**
		 * Load admin scripts.
		 *
		 * @since 1.0.0
		 */
		public function admin_enqueue_scripts_global() {
			$screen = get_current_screen();

			wp_register_style( 'awsm-team-admin-global', plugins_url( 'css/admin-global.css', $this->settings['plugin_file'] ), false, $this->settings['plugin_version'], 'all' );

			wp_register_script( 'awsm-team-admin-global', plugins_url( 'js/admin-global.js', $this->settings['plugin_file'] ), array( 'jquery', 'jquery-ui-sortable', 'wp-util' ), $this->settings['plugin_version'], true );

			if ( ! empty( $screen ) ) {
				$post_type = $screen->post_type;
				if ( ( $post_type === 'awsm_team' ) || ( $post_type === 'awsm_team_member' ) ) {
					wp_enqueue_style( 'awsm-team-admin-global' );
					wp_enqueue_script( 'awsm-team-admin-global' );
				}
			}

			wp_localize_script(
				'awsm-team-admin-global',
				'awsmTeamProAdmin',
				array(
					'nonce' => wp_create_nonce( 'awsm-team-pro-admin-nonce' ),
					'i18n'  => array(),
				)
			);
		}

		/**
		 * Adding submenu items.
		 *
		 *  @since 1.0.0
		 */
		public function add_submenu_items() {
			add_submenu_page( 'edit.php?post_type=awsm_team_member', __( 'Add Team', 'awsm-team-pro' ), __( 'Add Team', 'awsm-team-pro' ), 'manage_options', 'post-new.php?post_type=awsm_team' );
			add_submenu_page( 'edit.php?post_type=awsm_team_member', __( 'Settings', 'awsm-team-pro' ), __( 'Settings', 'awsm-team-pro' ), 'manage_options', 'awsm-team-settings', array( $this, 'awsm_global_settings' ) );
		}

		/**
		 * Register meta box.
		 *
		 * @since 1.0.0
		 */
		public function register_metaboxes() {
			add_meta_box( 'member_details', __( 'Member Details', 'awsm-team-pro' ), array( $this, 'member_details_meta' ), 'awsm_team_member', 'normal', 'high' );
			add_meta_box( 'team_details', __( 'Team Details', 'awsm-team-pro' ), array( $this, 'team_details_meta' ), 'awsm_team', 'normal', 'high' );
			add_meta_box( 'choose_team', __( 'Add to team', 'awsm-team-pro' ), array( $this, 'choose_member_teams' ), 'awsm_team_member', 'side', 'core' );
		}

		/**
		 * Meta box display callback - Member details.
		 *
		 * @since 1.0.0
		 * @param WP_Post $post Current post object.
		 */
		public function member_details_meta( $post ) {
			wp_nonce_field( basename( __FILE__ ), 'awsm_meta_details' );
			$awsm_contact = get_post_meta( $post->ID, 'awsm_contact', true );
			$awsm_social  = get_post_meta( $post->ID, 'awsm_social', true );
			$socialicons  = array( 'mail', 'link', 'phone', 'google-plus', 'google-plus2', 'hangouts', 'google-drive', 'facebook', 'facebook2', 'instagram', 'whatsapp', 'twitter', 'youtube', 'vimeo', 'vimeo2', 'flickr', 'flickr2', 'dribbble', 'behance', 'behance2', 'dropbox', 'wordpress', 'blogger', 'tumblr', 'tumblr2', 'skype', 'linkedin2', 'linkedin', 'stackoverflow', 'pinterest2', 'pinterest', 'foursquare', 'github', 'flattr', 'xing', 'xing2', 'stumbleupon', 'stumbleupon2', 'delicious', 'lastfm', 'lastfm2', 'hackernews', 'reddit', 'soundcloud', 'soundcloud2', 'yahoo', 'blogger2', 'ello', 'wordpress2', 'steam', 'steam2', '500px', 'deviantart', 'twitch', 'feed', 'feed2', 'sina-weibo', 'renren', 'vk', 'vine', 'telegram', 'spotify', 'mail2', 'mail3', 'imdb', 'discord', 'slack', 'viber', 'yelp', 'quora', 'meetup', 'mixer', 'snapchat-ghost', 'wechat' );
			include $this->settings['plugin_path'] . 'includes/member-details.php';
		}

		/**
		 * Meta box display callback - Team details.
		 *
		 * @since 1.0.0
		 * @param WP_Post $post Current post object.
		 */
		public function team_details_meta( $post ) {
			wp_nonce_field( basename( __FILE__ ), 'awsm_meta_details' );
			$args         = array(
				'post_type'      => 'awsm_team_member',
				'posts_per_page' => -1,
				'post_status'    => 'publish',
			);
			$members      = new WP_Query( $args );
			$options      = $this->get_options( 'awsm_team', $post->ID );
			$defaultimage = $this->settings['plugin_url'] . 'images/default-user.png';
			include $this->settings['plugin_path'] . 'includes/team-details.php';
		}

		/**
		 * Meta box display callback - Choose Team.
		 *
		 * @since 1.0.0
		 * @param WP_Post $post Current post object.
		 */
		public function choose_member_teams( $post ) {
			wp_nonce_field( basename( __FILE__ ), 'awsm_meta_details1' );
			$teams        = $this->get_teams();
			$member_teams = $this->get_teams_of_member( $post->ID );
			include $this->settings['plugin_path'] . 'includes/choose-teams.php';
		}

		/**
		 * Save metabox.
		 *
		 * @since 1.0.0
		 * @param int    $post_id id of the post.
		 * @param object $post Post Object.
		 */
		public function save_metabox_data( $post_id, $post ) {
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return;
			}

			if ( ! isset( $_POST['awsm_meta_details'] ) || ! wp_verify_nonce( sanitize_key( $_POST['awsm_meta_details'] ), basename( __FILE__ ) ) ) {
				return $post_id;
			}

			$post_type = get_post_type_object( $post->post_type );
			if ( ! current_user_can( $post_type->cap->edit_post, $post_id ) ) {
				return $post_id;
			}

			$team_meta = array();
			if ( $post->post_type == 'awsm_team_member' ) {
				$team_meta = array(
					'awsm-team-designation',
					'awsm-team-short-desc',
					'awsm-member-teams',
				);

				$team_repeater = array(
					'awsm_contact' => array(
						'label'   => 'awsm-team-label',
						'content' => 'awsm-team-content',
					),
					'awsm_social'  => array(
						'icon' => 'awsm-team-icon',
						'link' => 'awsm-team-link',
					),
				);
				foreach ( $team_repeater as $key => $value ) {
					$olddata = get_post_meta( $post_id, $key, true );
					$newdata = $item = array(); // phpcs:ignore Squiz.PHP.DisallowMultipleAssignments.Found
					foreach ( $value as $sub_key => $sub_value ) {
						$item[ $sub_key ] = isset( $_POST[ $sub_value ] ) ? wp_unslash( $_POST[ $sub_value ] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
					}
					$count = count( reset( $item ) );
					for ( $i = 0; $i < $count; $i++ ) {
						foreach ( $value as $sub_key => $sub_value ) {
							if ( $item[ $sub_key ][ $i ] != '' ) {
								$sanitized_value = $item[ $sub_key ][ $i ];
								if ( $sub_key === 'content' ) {
									$sanitized_value = wp_kses( $sanitized_value, 'post' );
								} elseif ( $sub_key === 'link' ) {
									if ( filter_var( $sanitized_value, FILTER_VALIDATE_EMAIL ) ) {
										$sanitized_value = sanitize_email( $sanitized_value );
									} elseif ( $this->validate_phone_number( $sanitized_value ) ) {
										$sanitized_value = sanitize_text_field( $sanitized_value );
									} else {
										$sanitized_value = esc_url_raw( $sanitized_value );
									}
								} else {
									$sanitized_value = sanitize_text_field( $sanitized_value );
								}
								$newdata[ $i ][ $sub_key ] = $sanitized_value;
							}
						}
					}
					if ( ! empty( $newdata ) && $newdata != $olddata ) {
						update_post_meta( $post_id, $key, $newdata );
					} elseif ( empty( $newdata ) && $olddata ) {
						delete_post_meta( $post_id, $key, $olddata );
					}
				}
			} elseif ( $post->post_type == 'awsm_team' ) {
				$team_meta = array( 'memberlist', 'team-style', 'preset', 'columns', 'custom_css', 'enable_filter', 'team_filters', 'awsm_member_order', 'awsm_member_order_by' );
			}
			foreach ( $team_meta as $meta_key ) {
				$olddata = get_post_meta( $post_id, $meta_key, true );
				$newdata = array();
				if ( isset( $_POST[ $meta_key ] ) ) {
					if ( is_array( $_POST[ $meta_key ] ) ) {
						if ( $meta_key === 'awsm-member-teams' && ! empty( $_POST[ $meta_key ] ) ) {
							$team_ids        = isset( $_POST['awsm-team-list'] ) ? array_map( 'intval', wp_unslash( $_POST['awsm-team-list'] ) ) : array();
							$member_team_ids = array_map( 'intval', wp_unslash( $_POST[ $meta_key ] ) );

							$this->update_member_list( $post_id, $member_team_ids, $team_ids );
							$newdata = implode( ',', $member_team_ids );
						} elseif ( $meta_key === 'memberlist' && ! empty( $_POST[ $meta_key ] ) ) {
							$member_ids      = isset( $_POST['members_list'] ) ? array_map( 'intval', wp_unslash( $_POST['members_list'] ) ) : array();
							$team_member_ids = array_map( 'intval', wp_unslash( $_POST[ $meta_key ] ) );

							$this->update_member_teams( $post_id, $team_member_ids, $member_ids );
							$newdata = $team_member_ids;
						} else {
							$newdata = array_map( 'wp_strip_all_tags', wp_unslash( $_POST[ $meta_key ] ) );
						}
					} else {
						if ( $meta_key === 'awsm-team-designation' || $meta_key === 'awsm-team-short-desc' ) {
							$newdata = wp_kses( wp_unslash( $_POST[ $meta_key ] ), 'post' );
						} else {
							$newdata = wp_strip_all_tags( wp_unslash( $_POST[ $meta_key ] ) );
						}
					}
					if ( ! empty( $newdata ) && $newdata != $olddata ) {
						update_post_meta( $post_id, $meta_key, $newdata );
					} elseif ( empty( $newdata ) && $olddata ) {
						delete_post_meta( $post_id, $meta_key, $olddata );
					}
				} else {
					if ( $meta_key === 'awsm-member-teams' && isset( $_POST['awsm-team-list'] ) ) {
						$team_ids = isset( $_POST['awsm-team-list'] ) ? array_map( 'intval', wp_unslash( $_POST['awsm-team-list'] ) ) : array();

						$this->update_member_list( $post_id, '', $team_ids );
						delete_post_meta( $post_id, $meta_key, $olddata );
					} elseif ( $meta_key === 'memberlist' && isset( $_POST['members_list'] ) ) {
						$member_ids = isset( $_POST['members_list'] ) ? array_map( 'intval', wp_unslash( $_POST['members_list'] ) ) : array();

						$this->update_member_teams( $post_id, '', $member_ids );
						delete_post_meta( $post_id, $meta_key, $olddata );
					} else {
						delete_post_meta( $post_id, $meta_key, $olddata );
					}
				}
			}
		}

		/**
		 * Update teams of a member.
		 *
		 * @since 1.0.0
		 * @param  int   $team_id ID of the post.
		 * @param array $member_ids Team Member IDs.
		 * @param array $members Member IDs collection.
		 */
		public function update_member_teams( $team_id, $member_ids = null, $members ) {
			$teams_of_member_arr = array();
			$teams_of_member     = '';
			if ( empty( $member_ids ) ) {
				$allmems = $this->get_members();
				foreach ( $allmems as $key => $member_id ) {
					$teams_of_member = explode( ',', get_post_meta( $key, 'awsm-member-teams', true ) );

					if ( $teams_of_member ) {
						if ( in_array( $team_id, $teams_of_member ) ) {

							$this->unset_team( $allmems, $team_id, $members );
						}
					}
				}
			} else {
				if ( $member_ids ) {
					foreach ( $member_ids as $member_id ) {
						$teams_of_member = array_keys( $this->get_teams_of_member( $member_id ) );

						$teams_of_member     = explode( ',', get_post_meta( $member_id, 'awsm-member-teams', true ) );
						$teams_of_member     = array_filter( $teams_of_member );
						$teams_of_member_arr = $teams_of_member;
						if ( $teams_of_member ) {
							if ( ! in_array( $team_id, $teams_of_member ) ) {
								$teams_of_member_arr[] = $team_id;

							} else {
								$this->unset_team( $member_ids, $team_id, $members );

							}
						} else {
							$teams_of_member_arr[] = $team_id;
						}
						update_post_meta( $member_id, 'awsm-member-teams', implode( ',', $teams_of_member_arr ) );
					}
				}
			}
		}

		/**
		 * Update members of a team.
		 *
		 * @since 1.0.0
		 * @param int   $member_id ID of the post.
		 * @param array $team_ids Member Team IDs.
		 * @param array $teams Team IDs collection.
		 */
		public function update_member_list( $member_id, $team_ids = null, $teams ) {
			if ( empty( $team_ids ) ) {
				$allteams = $this->get_teams();
				foreach ( $allteams as $key => $team_id ) {
					$teams_of_member = get_post_meta( $key, 'memberlist', true );
					if ( $teams_of_member ) {
						$teams_of_member_arr = $this->array_flatten( get_post_meta( $key, 'memberlist', false ) );

						if ( in_array( $member_id, $teams_of_member_arr ) ) {
							$this->unset_member( $teams, $team_ids, $member_id );
						}
					}
				}
			} else {
				$teams_of_member_arr = array();
				foreach ( $team_ids as $key => $team_id ) {
					$teams_of_member = get_post_meta( $team_id, 'memberlist', true );
					if ( $teams_of_member ) {
						$teams_of_member_arr = $this->array_flatten( get_post_meta( $team_id, 'memberlist', false ) );
						if ( ! in_array( $member_id, $teams_of_member_arr ) ) {
							// update member meta.
							$teams_of_member_arr[] = $member_id;
						} else {
							$this->unset_member( $teams, $team_ids, $member_id );
						}
					} else {
						$teams_of_member_arr[] = $member_id;
					}
					update_post_meta( $team_id, 'memberlist', $teams_of_member_arr );
					$teams_of_member_arr[] = '';
				}
			}
		}

		/**
		 * Update teams of a member.
		 *
		 * @since 1.0.0
		 * @param array $member_ids Team Member IDs.
		 * @param int   $team_id Team ID.
		 * @param array $members Member IDs collection.
		 */
		public function unset_team( $member_ids, $team_id, $members ) {
			if ( ! empty( $member_ids ) ) {
				$unchecked_members = array_diff( $members, $member_ids );
			} else {
				$unchecked_members = $members;
			}
			$teamargs     = array(
				'post_type'      => 'awsm_team_member',
				'post__in'       => $unchecked_members,
				'posts_per_page' => -1,
				'meta_query'     => array(
					array(
						'key'     => 'awsm-member-teams',
						'value'   => $team_id,
						'compare' => 'REGEXP',
					),
				),
			);
			$member_teams = new WP_Query( $teamargs );
			if ( $member_teams->have_posts() ) :
				while ( $member_teams->have_posts() ) :
					$member_teams->the_post();

					$team_list  = explode( ',', get_post_meta( $member_teams->post->ID, 'awsm-member-teams', true ) );
					$team_index = array_search( $team_id, $team_list );
					if ( $team_index !== false ) {
						unset( $team_list[ $team_index ] );
						update_post_meta( $member_teams->post->ID, 'awsm-member-teams', implode( ',', $team_list ) );
					}
				endwhile;
			endif;
			wp_reset_postdata();
		}

		/**
		 * Update members of a team.
		 *
		 * @since 1.0.0
		 * @param array $teams Team IDs.
		 * @param array $team_ids Member Team IDs.
		 * @param int   $member_id The Member ID.
		 */
		public function unset_member( $teams, $team_ids = null, $member_id ) {
			if ( ! empty( $team_ids ) ) {
				$unchecked_teams = array_diff( $teams, $team_ids );
			} else {
				$unchecked_teams = $teams;
			}

			$teamargs = array(
				'post_type'      => 'awsm_team',
				'post__in'       => $unchecked_teams,
				'posts_per_page' => -1,
				'meta_query'     => array(
					array(
						'key'     => 'memberlist',
						'value'   => $member_id,
						'compare' => 'REGEXP',
					),
				),
			);

			$member_teams = new WP_Query( $teamargs );
			if ( $member_teams->have_posts() ) :
				while ( $member_teams->have_posts() ) :
					$member_teams->the_post();
					$member_list  = $this->array_flatten( get_post_meta( $member_teams->post->ID, 'memberlist', false ) );
					$member_index = array_search( $member_id, $member_list );
					if ( $member_index !== false ) {
						unset( $member_list[ $member_index ] );
						update_post_meta( $member_teams->post->ID, 'memberlist', $member_list );
					}
				endwhile;
			endif;

			wp_reset_postdata();
		}

		/**
		 * Convert a multi-dimensional array into a single-dimensional array.
		 *
		 * @param  array $array The multi-dimensional array.
		 * @return array
		 */
		public function array_flatten( $array ) {
			if ( ! is_array( $array ) ) {
				return false;
			}
			$result = array();
			foreach ( $array as $key => $value ) {
				if ( is_array( $value ) ) {
					$result = array_merge( $result, $this->array_flatten( $value ) );
				} else {
					$result[ $key ] = $value;
				}
			}
			return $result;
		}

		/**
		 * Dropdown Builder.
		 *
		 * @since 1.0.0
		 * @param string $name Field name.
		 * @param array  $options Dropdown Options.
		 * @param string $selected Selected Value.
		 * @param string $selecttext Dropdown field text.
		 * @param string $class HTML class for the field.
		 * @param string $optionvalue Value to be selected for options.
		 */
		public function selectbuilder( $name, $options, $selected = '', $selecttext = '', $class = '', $optionvalue = 'value' ) {
			if ( is_array( $options ) ) :
				$select_html = "<select name=\"$name\" id=\"$name\" class=\"$class\">";
				if ( $selecttext ) {
					$select_html .= '<option value="">' . $selecttext . '</option>';
				}
				foreach ( $options as $key => $option ) {
					if ( $optionvalue == 'value' ) {
						$value = $option;
					} else {
						$value = $key;
					}
					$select_html .= "<option value=\"$value\"";
					if ( $value == $selected ) {
						$select_html .= ' selected="selected"';
					}
					$select_html .= ">$option</option>\n";
				}
				$select_html .= '</select>';
				echo wp_kses(
					$select_html,
					array(
						'select' => array(
							'name'  => array(),
							'id'    => array(),
							'class' => array(),
						),
						'option' => array(
							'value'    => array(),
							'selected' => array(),
						),
					)
				);
			endif;
		}

		/**
		 * Get options.
		 *
		 * @since 1.0.0
		 * @param string $postype Post type slug.
		 * @param int    $post_id ID of post.
		 */
		public function get_options( $postype, $post_id ) {
			$post = get_post( $post_id );

			if ( ! $post ) {
				return false;
			}

			$metakeys['awsm_team_member'] = array(
				'awsm_contact',
				'awsm_social',
				'awsm-team-designation',
				'awsm-team-short-desc',
			);
			$metakeys['awsm_team']        = array(
				'memberlist',
				'team-style',
				'preset',
				'columns',
				'custom_css',
				'enable_filter',
				'team_filters',
				'awsm_member_order',
				'awsm_member_order_by',
			);
			$options['awsm_team_member']  = array(
				'awsm_contact'          => array(),
				'awsm_social'           => array(),
				'awsm-team-designation' => '',
				'awsm-team-short-desc'  => '',
			);
			$options['awsm_team']         = array(
				'memberlist'           => array(),
				'team-style'           => 'drawer',
				'preset'               => 'style-1',
				'columns'              => '2',
				'custom_css'           => '',
				'enable_filter'        => 0,
				'team_filters'         => '',
				'awsm_member_order'    => '',
				'awsm_member_order_by' => 'drag-and-drop',
			);
			foreach ( $metakeys[ $postype ] as $key => $value ) {
				$metavalue = get_post_meta( $post_id, $value, true );
				if ( $metavalue ) {
					$options[ $postype ][ $value ] = $metavalue;
				}
			}
			return $options[ $postype ];
		}

		/**
		 * Get team thumbnail.
		 *
		 * @since 1.0.0
		 * @param int    $team_id  Post id of team.
		 * @param string $thumbnail thumbnail size.
		 */
		public function team_thumbnail( $team_id, $thumbnail = 'awsm_team' ) {
			$defaultimage = $this->settings['plugin_url'] . 'images/default-user.png';
			$member_image = get_post_thumbnail_id( $team_id );
			if ( $member_image ) {
				$member_image_url = wp_get_attachment_image_src( $member_image, $thumbnail, true );
				$member_image_url = $member_image_url[0];
			} else {
				$member_image_url = $defaultimage;
			}
			return esc_url( $member_image_url );
		}

		/**
		 * Get team thumbnail.
		 *
		 * @since 1.0.0
		 * @param int    $team_id Post id of team.
		 * @param string $thumbnail thumbnail size.
		 */
		public function get_team_thumbnail( $team_id, $thumbnail = 'awsm_team' ) {
			$defaultimage = '<img src="' . $this->settings['plugin_url'] . 'images/default-user.png" alt="' . esc_attr( get_the_title( $team_id ) ) . '">';
			$member_image = ( has_post_thumbnail( $team_id ) ) ? get_the_post_thumbnail( $team_id, $thumbnail ) : $defaultimage;
			return $member_image;
		}

		/**
		 * Item styling class name generator.
		 *
		 * @since 1.0.0
		 * @param array  $options Class names collection.
		 * @param string $custom Custom class name.
		 * @return string
		 */
		public function item_style( $options, $custom = '' ) {
			$style = array(
				$options['team-style'] . '-style',
				$options['preset'],
				'grid-' . $options['columns'] . '-col',
				$custom,
			);
			return implode( ' ', $style );
		}

		/**
		 * Class generator.
		 *
		 * @since 1.0.0
		 * @param array $class classnames.
		 */
		public function addclass( $class ) {
			return implode( ' ', $class );
		}

		/**
		 * ID generator.
		 *
		 * @since 1.0.0
		 * @param array $id Unique HTML ID Collection.
		 */
		public function add_id( $id ) {
			return implode( '-', $id );
		}

		/**
		 * Print the meta data after checking it's existence.
		 *
		 * @since 1.0.0
		 * @param string         $template The format string.
		 * @param boolean|string $value Value for output.
		 * @param boolean        $return Whether to return the value or not.
		 */
		public function checkprint( $template, $value, $return = false ) {
			if ( $value ) {
				if ( $return ) {
					return sprintf( $template, $value );
				} else {
					echo sprintf( $template, $value ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				}
			}
		}

		/**
		 * Grab all attributes for a given shortcode in a text.
		 *
		 * @since 1.1.0
		 * @param  string $tag  Shortcode tag.
		 * @param  string $text Text containing shortcodes.
		 * @return array  $out Array of attributes.
		 */
		public function get_all_attributes( $tag, $text ) {
			preg_match_all( '/\[awsmteam(.+?)?\]/', $text, $matches );
			$out = array();
			if ( isset( $matches[1] ) ) {
				foreach ( (array) $matches[1] as $key => $value ) {
						$out[] = shortcode_parse_atts( $value );
				}
			}
			return $out;
		}

		/**
		 * Apply content filter on widgets.
		 *
		 * @since 1.1.1
		 * @param  string $content Widget Content.
		 * @return string
		 */
		public function widget_text_filter( $content ) {
			$pattern = '/\[awsmteam(.+?)?\]/';

			if ( ! preg_match( $pattern, $content ) ) {
				return $content;
			}

			$content = do_shortcode( $content );

			return $content;
		}

		/**
		 * Create Team Request Handler.
		 *
		 * @return void
		 */
		public function awsm_create_team() {
			/* First, check nonce */
			check_ajax_referer( 'ajax_nonce', 'security' );

			// now, handle the request.
			$team_id   = false;
			$output    = '';
			$user_id   = get_current_user_id();
			$member_id = isset( $_POST['member_id'] ) ? intval( $_POST['member_id'] ) : 0;
			$team_name = isset( $_POST['team_name'] ) ? sanitize_text_field( wp_unslash( $_POST['team_name'] ) ) : '';

			if ( ! $member_id || get_post_type( $member_id ) !== 'awsm_team_member' ) {
				$output = esc_html__( 'Not a valid member ID!', 'awsm-team-pro' );
			} else {
				$post_type = get_post_type_object( 'awsm_team_member' );
				if ( ! current_user_can( $post_type->cap->edit_post, $member_id ) ) {
					$output = esc_html__( 'Not authorized to make this request!', 'awsm-team-pro' );
				}
			}

			if ( empty( $team_name ) ) {
				$output = esc_html__( 'Team name cannot be emtpy!', 'awsm-team-pro' );
			}

			if ( empty( $output ) ) {
				$new_team = array(
					'post_title'  => $team_name,
					'post_status' => 'publish',
					'post_author' => $user_id,
					'post_type'   => 'awsm_team',
				);
				$team_id  = wp_insert_post( $new_team );
				if ( ! is_wp_error( $team_id ) ) {
					// the post is valid.
					$output .= "\n" . '<li style="background-color: #FFFF00">' .
					'<input type="checkbox" name="awsm-member-teams[]" value="' . $team_id . '" checked />' . $team_name . '</li>';
				} else {
					$team_id = false;
					$output  = esc_html__( 'Error while creating new team', 'awsm-team-pro' );
				}
			}

			wp_send_json(
				array(
					'team_id' => $team_id,
					'output'  => wp_kses(
						$output,
						array(
							'li'    => array(
								'style' => array(
									'background-color' => array(),
								),
							),
							'input' => array(
								'type'    => array(),
								'name'    => array(),
								'value'   => array(),
								'checked' => array(),
							),
						)
					),
				)
			);
		}

		/**
		 * Update member request handler.
		 *
		 * @return void
		 */
		public function update_member_dropdown() {
			/* First, check nonce */
			check_ajax_referer( 'ajax_nonce', 'security' );

			// now, handle the request.
			$members_list = '';
			$error        = false;
			$team_id      = isset( $_POST['team_id'] ) ? intval( $_POST['team_id'] ) : 0;

			if ( ! $team_id || get_post_type( $team_id ) !== 'awsm_team' ) {
				$error = esc_html__( 'Not a valid team ID!', 'awsm-team-pro' );
			} else {
				$post_type = get_post_type_object( 'awsm_team' );
				if ( ! current_user_can( $post_type->cap->edit_post, $team_id ) ) {
					$error = esc_html__( 'Not authorized to make this request!', 'awsm-team-pro' );
				}
			}

			if ( ! isset( $_POST['order'] ) || ! isset( $_POST['orderby'] ) || ! isset( $_POST['memberlist'] ) || empty( $_POST['memberlist'] ) ) {
				$error = esc_html__( 'Invalid request!', 'awsm-team-pro' );
			}

			if ( ! $error ) {
				$order   = sanitize_text_field( wp_unslash( $_POST['order'] ) );
				$orderby = sanitize_text_field( wp_unslash( $_POST['orderby'] ) );
				$members = array_map( 'intval', wp_unslash( $_POST['memberlist'] ) );

				if ( 'drag-and-drop' === $order ) {
					$teamargs = array(
						'post_type'      => 'awsm_team_member',
						'post__in'       => $members,
						'posts_per_page' => -1,
						'orderby'        => 'post__in',
					);
				} else {
					$teamargs = array(
						'post_type'      => 'awsm_team_member',
						'post__in'       => $members,
						'posts_per_page' => -1,
						'orderby'        => $orderby,
						'order'          => $order,
					);
				}

				$team = new WP_Query( $teamargs );

				if ( $team->have_posts() ) :
					while ( $team->have_posts() ) :
						$team->the_post();

						$members_list .= "<li data-member-id='" . esc_attr( $team->post->ID ) . "' class=''>
						<img width='31' height='31' src='" . esc_url( $this->team_thumbnail( $team->post->ID, 'thumbnail' ) ) . "' />
						<p>" . esc_html( get_the_title() ) . "</p><span class='remove-member-to-list' data-member='" . esc_attr( $team->post->ID ) . "'><i class='awsm-icon-close'></i></span>
						<input type='hidden' name='memberlist[]' value='" . esc_attr( $team->post->ID ) . "'>
						</li>";
					endwhile;
					wp_reset_postdata();
				endif;
			}

			wp_send_json(
				array(
					'memberlist' => $members_list,
					'error'      => $error,
				)
			);
		}

		/**
		 * Get the path to template.
		 *
		 * @param string         $template_name Template name.
		 * @param string|boolean $sub_dir_name The sub directory.
		 * @return string
		 */
		public function get_template_path( $template_name, $sub_dir_name = false ) {
			$path        = $rel_path = ''; // phpcs:ignore Squiz.PHP.DisallowMultipleAssignments.Found
			$plugin_base = 'awsm-team-pro';
			if ( ! empty( $sub_dir_name ) ) {
				$rel_path .= "/{$sub_dir_name}";
			}
			$rel_path      .= "/{$template_name}";
			$theme_base_dir = trailingslashit( get_stylesheet_directory() );
			if ( file_exists( $theme_base_dir . $plugin_base . $rel_path ) ) {
				$path = $theme_base_dir . $plugin_base . $rel_path;
			} else {
				$path = $this->settings['plugin_path'] . 'templates' . $rel_path;
			}
			return $path;
		}

		/**
		 * Remove the srcset attribute from post thumbnails that are called with the 'awsm_team' size string: the_post_thumbnail( 'awsm_team' )
		 *
		 * @param string|array $size The post thumbnail size.
		 * @return string
		 */
		public function awsm_remove_srcset( $size ) {
			if ( is_string( $size ) && 'awsm_team' == $size ) {
				add_filter(
					'wp_calculate_image_srcset_meta',
					array( $this, 'awsm_disable_srceset' )
				);
			}
			return $size;
		}

		/**
		 * Remove image src meta.
		 *
		 * @param array $image_meta The image meta data.
		 * @return null
		 */
		public function awsm_disable_srceset( $image_meta ) {
			remove_filter( current_filter(), __FUNCTION__ );
			return null;
		}

		/**
		 * Phone number validation.
		 *
		 * @param string $phone The phone number.
		 * @return boolean
		 */
		public function validate_phone_number( $phone ) {
			if ( preg_match( '/^(?=.*[0-9])[- +()0-9]{4,15}+$/', $phone ) ) {
				return true;
			} else {
				return false;
			}
		}

		/**
		 * Register Team Guten Blocks.
		 *
		 * @return void
		 */
		public function awsm_team_blocks() {
			wp_register_script(
				'gutenberg-awsm-team',
				plugins_url( 'blocks/team-block.js', __FILE__ ),
				array( 'wp-blocks', 'wp-element', 'wp-data', 'wp-components', 'wp-editor', 'wp-i18n', 'atp_choose_team' ),
				$this->settings['plugin_version'],
				true
			);

			if ( function_exists( 'register_block_type' ) ) {
				register_block_type(
					'gutenberg-awsm/awsm-team-dynamic',
					array(
						'attributes'      => array(
							'className' => array(
								'type' => 'string',
							),
							'shortcode' => array(
								'type' => 'string',
							),
						),
						'editor_script'   => 'gutenberg-awsm-team',
						'render_callback' => array( $this, 'awsm_team_render_callback' ),
					)
				);
			}
		}

		/**
		 * Server side rendering.
		 *
		 * @param array $atts Block attributes.
		 * @return string Block output content.
		 */
		public function awsm_team_render_callback( $atts ) {
			if ( ! isset( $atts['shortcode'] ) ) {
				return;
			}
			$id      = $atts['shortcode'];
			$options = $this->get_options( 'awsm_team', $id );
			if ( ! $options ) {
				return '<div class="awsm-team-error">' . __( 'Team not found', 'awsm-team-pro' ) . '</div>';
			}
			if ( empty( $options['memberlist'] ) ) {
				return '<div class="awsm-team-error">' . __( 'No members found', 'awsm-team-pro' ) . '</div>';
			}
			$template = $this->get_template_path( $options['team-style'] . '.php' );
			if ( file_exists( $template ) ) {
				ob_start();
				if ( 'drag-and-drop' === $options['awsm_member_order_by'] ) {
					$teamargs = array(
						'post_type'      => 'awsm_team_member',
						'post__in'       => $options['memberlist'],
						'posts_per_page' => -1,
						'orderby'        => 'post__in',
					);
				} else {
					$teamargs = array(
						'post_type'      => 'awsm_team_member',
						'post__in'       => $options['memberlist'],
						'posts_per_page' => -1,
						'orderby'        => $options['awsm_member_order_by'],
						'order'          => $options['awsm_member_order'],
					);
				}
				$team = new WP_Query( $teamargs );
				include $template;
				wp_reset_postdata();
				return ob_get_clean();
			}
		}

		/**
		 * Team Popup content.
		 *
		 * @return void
		 */
		public function choose_team() {
			if ( wp_script_is( 'atp_choose_team' ) ) {
				add_thickbox();
				include $this->settings['plugin_path'] . 'includes/popup.php';
			}
		}

		/**
		 * Load team scripts.
		 *
		 * @return void
		 */
		public function team_helper() {
			$script_deps = array( 'jquery' );
			if ( function_exists( 'get_current_screen' ) ) {
				$screen = get_current_screen();
				if ( method_exists( $screen, 'is_block_editor' ) && $screen->is_block_editor() ) {
					$script_deps[] = 'wp-blocks';
				}
			}
			wp_enqueue_script( 'atp_choose_team', plugins_url( 'js/awsm-block.js', $this->settings['plugin_file'] ), $script_deps, $this->settings['plugin_version'], true );
			wp_enqueue_style( 'atp_choose_team', plugins_url( 'css/awsm-team-block.css', $this->settings['plugin_file'] ), false, $this->settings['plugin_version'], 'all' );
			wp_localize_script( 'atp_choose_team', 'team_settings', $this->team_data() );
		}

		/**
		 * Localize array.
		 *
		 * @return array
		 */
		public function team_data() {
			$team_data = array(
				'insert_text' => __( 'Select', 'awsm-team-pro' ),
				'nocontent'   => __( 'Nothing to insert', 'awsm-team-pro' ),
				'pluginname'  => __( 'AWSM Team', 'awsm-team-pro' ),
			);
			return $team_data;
		}

		/**
		 * Plugin update notice.
		 *
		 * @return void
		 */
		public function after_plugin_row() {
			$license_key = get_option( 'awsm_team_pro_license' );
			if ( ! $license_key ) :
				?>
				<tr class="plugin-update-tr active">
					<td colspan="3" class="plugin-update colspanchange">
						<div class="update-message notice inline notice-warning notice-alt">
							<?php
								$html         = self::get_automatic_update_notice();
								$version_data = self::get_latest_version();
							if ( version_compare( $this->settings['plugin_version'], $version_data['version'], '<' ) ) {
								$plugin = esc_html__( 'AWSM Team Pro', 'awsm-team-pro' );
								/* translators: %1$s: plugin latest version, %2$s: plugin name, %3$s: html content */
								$html = sprintf( esc_html__( 'There is a new version %1$s of %2$s available. %3$s', 'awsm-team-pro' ), esc_html( $version_data['version'] ), $plugin, $html );
							}
								printf( '<p>%s</p>', $html ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							?>
						</div>
					</td>
				</tr>
				<?php
			endif;
		}

		/**
		 * Get automatic update notice for the plugin.
		 *
		 * @return string
		 */
		public static function get_automatic_update_notice() {
			$url = admin_url( 'edit.php?post_type=awsm_team_member&page=awsm-team-settings' );
			/* translators: %1$s: Opening anchor tag, %2$s: closing anchor tag */
			$notice = sprintf( esc_html__( 'Please %1$s activate your copy%2$s of AWSM Team Pro to receive automatic updates.', 'awsm-team-pro' ), sprintf( '<a href="%s">', esc_url( $url ) ), '</a>' );
			return $notice;
		}

		/**
		 * Get the latest plugin version from Kernl API.
		 *
		 * @return array
		 */
		public function get_latest_version() {
			$version_data = array(
				'version' => $this->settings['plugin_version'],
			);
			if ( get_transient( '_awsm_team_pro_latest_version_data' ) === false ) {
				$options  = array(
					'timeout' => 10,
					'headers' => array(
						'Accept' => 'application/json',
					),
				);
				$response = wp_remote_get( self::get_kernl_url( 'latest-version' ), $options );
				if ( ! is_wp_error( $response ) ) {
					$response_body = wp_remote_retrieve_body( $response );
					if ( ! is_wp_error( $response_body ) ) {
						if ( wp_remote_retrieve_response_code( $response ) === 200 ) {
							set_transient( '_awsm_team_pro_latest_version_data', $response_body, HOUR_IN_SECONDS );
						}
					}
				}
			}
			$json     = get_transient( '_awsm_team_pro_latest_version_data' );
			$api_data = json_decode( $json, true );
			if ( ! empty( $api_data ) && isset( $api_data['version'] ) ) {
				$version_data = $api_data;
			}
			return $version_data;
		}

		/**
		 * Plugin update handler.
		 *
		 * @return void
		 */
		public function update_plugin() {
			$license_key = get_option( 'awsm_team_pro_license' );
			if ( ! empty( $license_key ) ) {
				require_once $this->settings['plugin_path'] . '/lib/plugin-update-check.php';
				$pro_update_checker          = new AWSM_Team_Pro_Update_Checker(
					self::get_kernl_url(),
					__FILE__,
					'awsm-team-pro',
					1
				);
				$pro_update_checker->license = $license_key;
			}
		}

		/**
		 * Get the Kernl URL for the plugin.
		 *
		 * @param string $endpoint_base Endpoint.
		 * @return string
		 */
		public static function get_kernl_url( $endpoint_base = 'updates' ) {
			return esc_url( self::$kernl_base_url . '/' . $endpoint_base . '/' . self::$uuid . '/' );
		}

		/**
		 * Get the Deep Linking settings.
		 *
		 * @return array
		 */
		public static function get_deep_linking_settings() {
			$defaults = array(
				'enable' => '',
				'member' => array(
					'prefix' => 'member',
					'suffix' => 'info',
				),
				'team'   => array(
					'prefix' => 'team',
					'suffix' => 'info',
				),
			);
			$settings = get_option( 'awsm_team_pro_deep_link_settings' );
			if ( empty( $settings ) || ! is_array( $settings ) ) {
				$settings = array();
			}
			return wp_parse_args( $settings, $defaults );
		}

		/**
		 * Deep linking data attribute.
		 *
		 * @param int     $id The member ID or the team filter term ID.
		 * @param boolean $is_member Is member attribute or not.
		 * @param boolean $echo Whether to echo the attribute or not.
		 * @return string
		 */
		public static function deep_link_attr( $id, $is_member = true, $echo = true ) {
			$attr     = '';
			$settings = self::get_deep_linking_settings();
			if ( ! empty( $settings ) && isset( $settings['enable'] ) && $settings['enable'] === 'enable' ) {
				$attr_val = $id;
				if ( ! $is_member ) {
					$prefix   = $settings['team']['prefix'];
					$suffix   = $settings['team']['suffix'];
					$attr_val = "#{$prefix}-{$id}";
					if ( trim( $suffix ) !== '' ) {
						$attr_val .= '-' . $suffix;
					}
				}
				$attr = sprintf( ' data-info="%s"', esc_attr( $attr_val ) );
			}

			/**
			 * Filters the Deep linking data attribute.
			 *
			 * @since 1.8.0
			 *
			 * @param string $attr The data attribute.
			 * @param int $id The Member ID or The Term ID.
			 * @param boolean $is_member Is member attribute or not.
			 */
			$attr = apply_filters( 'awsm_team_deep_link_attr', $attr, $id, $is_member );
			if ( $echo ) {
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo $attr;
			} else {
				return $attr;
			}
		}

		/**
		 * Team settings page.
		 *
		 * @return void
		 */
		public function awsm_global_settings() {
			include $this->settings['plugin_path'] . 'includes/team-settings.php';
		}

		/**
		 * Register team settings.
		 *
		 * @return void
		 */
		public function register_team_settings() {
			$settings = $this->settings();
			foreach ( $settings as $group => $settings_args ) {
				foreach ( $settings_args as $setting_args ) {
					register_setting( 'awsm-team-pro-' . $group . '-settings', $setting_args['option_name'], isset( $setting_args['callback'] ) ? $setting_args['callback'] : 'sanitize_text_field' );
				}
			}
		}

		/**
		 * Team settings.
		 *
		 * @return array
		 */
		private function settings() {
			$settings = array(
				'license' => array(
					array(
						'option_name' => 'awsm_team_pro_license',
						'callback'    => array( $this, 'license_handler' ),
					),
					array(
						'option_name' => 'awsm_team_pro_thumbnail_settings',
						'callback'    => array( $this, 'thumbnail_handler' ),
					),
					array(
						'option_name' => 'awsm_team_pro_deep_link_settings',
						'callback'    => array( $this, 'deep_link_handler' ),
					),
				),
			);
			return $settings;
		}

		/**
		 * License settings handler.
		 *
		 * @param string $license_key The license key.
		 * @return boolean|string
		 */
		public function license_handler( $license_key ) {
			if ( ! empty( $license_key ) ) {
				$license_key  = sanitize_text_field( $license_key );
				$options      = array(
					'timeout' => 10,
					'headers' => array(
						'Accept' => 'application/json',
					),
				);
				$args['code'] = rawurlencode( $license_key );
				$url          = add_query_arg( $args, self::get_kernl_url() );
				$result       = wp_remote_get( $url, $options );
				if ( ! is_wp_error( $result ) && isset( $result['response'] ) && isset( $result['response']['code'] ) && ( $result['response']['code'] === 200 ) ) {
					add_settings_error( 'awsm_team_pro_license', 'awsm-team-pro-license-settings', __( 'Thank you, your purchase code has been verified.', 'awsm-team-pro' ), 'updated' );
					return $license_key;
				} else {
					$invalid_text = sprintf( '<strong>%s</strong>%s(<a href="https://1.envato.market/team" target="_blank">%s</a>)', __( 'Something is wrong! ', 'awsm-team-pro' ), __( 'Either the purchase key is invalid or your support license is expired. ', 'awsm-team-pro' ), __( 'Renew Support', 'awsm-team-pro' ) );

					add_settings_error( 'awsm_team_pro_license', 'awsm-team-pro-license-settings', $invalid_text );
					return false;
				}
			}
			return $license_key;
		}

		/**
		 * Team thumbnail handler.
		 *
		 * @param mixed $thumbnail_options Thumbnail options.
		 * @return array
		 */
		public function thumbnail_handler( $thumbnail_options ) {
			$options = array(
				'enable_crop' => 'enable',
				'width'       => 500,
				'height'      => 500,
			);
			if ( ! empty( $thumbnail_options ) && is_array( $thumbnail_options ) ) {
				$options['enable_crop'] = isset( $thumbnail_options['enable_crop'] ) ? sanitize_text_field( $thumbnail_options['enable_crop'] ) : '';
				$options['width']       = isset( $thumbnail_options['width'] ) ? intval( $thumbnail_options['width'] ) : 500;
				$options['height']      = isset( $thumbnail_options['height'] ) ? intval( $thumbnail_options['height'] ) : 500;
			}
			return $options;
		}

		/**
		 * The Deep Linking Settings Handler.
		 *
		 * @param mixed $options The Deep linking options.
		 * @return array
		 */
		public function deep_link_handler( $options ) {
			if ( ! empty( $options ) && is_array( $options ) ) {
				$is_valid          = true;
				$options['enable'] = isset( $options['enable'] ) ? sanitize_text_field( $options['enable'] ) : '';
				$options['member'] = array_map( 'sanitize_text_field', $options['member'] );
				$options['team']   = array_map( 'sanitize_text_field', $options['team'] );
				if ( trim( $options['member']['prefix'] ) === '' || trim( $options['team']['prefix'] ) === '' ) {
					$is_valid = false;
					add_settings_error( 'awsm_team_pro_deep_link_settings', 'awsm-team-pro-license-settings', esc_html__( 'The prefix fields for Deep linking settings are required!', 'awsm-team-pro' ) );
				} else {
					if ( $options['member']['prefix'] === $options['team']['prefix'] ) {
						$is_valid = false;
						add_settings_error( 'awsm_team_pro_deep_link_settings', 'awsm-team-pro-license-settings', esc_html__( 'The member prefix and the team prefix cannot be the same.', 'awsm-team-pro' ) );
					} else {
						$url_fields = array_merge( array_values( $options['member'] ), array_values( $options['team'] ) );
						foreach ( $url_fields as $url_field ) {
							if ( ! empty( $url_field ) && ! preg_match( '/^([a-z0-9]+(-|_))*[a-z0-9]+$/', $url_field ) ) {
								$is_valid = false;
								add_settings_error( 'awsm_team_pro_deep_link_settings', 'awsm-team-pro-license-settings', esc_html__( 'The prefix/suffix should only contain alphanumeric, latin characters separated by hyphen/underscore, and cannot begin or end with a hyphen/underscore.', 'awsm-team-pro' ) );
								break;
							}
						}
					}
				}
				if ( ! $is_valid ) {
					$options = self::get_deep_linking_settings();
				}
			}
			return $options;
		}

		/**
		 * Display automatic update notice.
		 *
		 * @return void
		 */
		public function automatic_update_notice() {
			if ( current_user_can( 'install_plugins' ) && ! get_option( 'awsm_team_pro_license' ) ) {
				wp_enqueue_style( 'awsm-team-admin-global' );
				wp_enqueue_script( 'awsm-team-admin-global' );
				$is_dismissed = get_user_meta( get_current_user_id(), 'awsm_team_pro_activate_notice', true );
				if ( ! $is_dismissed ) {
					$nonce = wp_create_nonce( 'awsm-team-pro-admin-notice' );
                    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					printf( '<div class="awsm-pro-activate-notice notice notice-error notice" data-nonce="%2$s"><p>%1$s</p><a href="#" class="notice-dismiss">%3$s</a></div>', self::get_automatic_update_notice(), esc_attr( $nonce ), esc_html__( 'Dismiss', 'awsm-team-pro' ) );
				}
			}
		}

		/**
		 * Plugin notice dismiss handler.
		 *
		 * @return void
		 */
		public function awsm_team_pro_admin_notice() {
			$response    = array(
				'dismiss' => false,
				'error'   => array(),
			);
			$generic_msg = esc_html__( 'Error in dismissing the notice. Please try again!', 'awsm-team-pro' );
			if ( isset( $_POST['nonce'] ) && wp_verify_nonce( sanitize_key( $_POST['nonce'] ), 'awsm-team-pro-admin-notice' ) ) {
				if ( current_user_can( 'install_plugins' ) ) {
					$response['dismiss'] = update_user_meta( get_current_user_id(), 'awsm_team_pro_activate_notice', true );
				} else {
					$response['error'][] = esc_html__( 'You don&#8217;t have the permission to dismiss this notice!', 'awsm-team-pro' );
				}
			} else {
				$response['error'][] = $generic_msg;
			}
			wp_send_json( $response );
		}

		/**
		 * Add Elementor support.
		 *
		 * @return void
		 */
		public function elementor_support() {
			if ( did_action( 'elementor/loaded' ) ) {
				require_once $this->settings['plugin_path'] . '/includes/class-team-elementor-widget.php';
				// Register Elementor widget.
				\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new AWSMTeamElementorWidget() );
			}
		}

		/**
		 * Loads Beaver builder team builder modules.
		 */
		public function load_modules() {
			if ( ! class_exists( 'FLBuilder' ) ) {
				return;
			}
			require_once $this->settings['plugin_path'] . 'includes/beaver-builder/awsmteam/awsmteam.php';
		}
	}
	// Initialize the class.
	Awsm_Team::init();
endif; // class_exists check.
