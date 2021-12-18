<?php
/**
 * Team Details Meta.
 *
 * @package awsm-team-pro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="wrap">
	<div class="awsm-team-customize">
		<!-- <h2 class="awsm-sub-heading"></h2> -->
		<div class="awsm-team-customize-inner">
			<div class="awsm-team-customize-member">
				<div class="awsm-heading-group">
					<h2 class="sub-h"><?php esc_html_e( 'Members', 'awsm-team-pro' ); ?></h2>
					<span><?php esc_html_e( 'Select members from the dropdown, drag and drop them to reorder.', 'awsm-team-pro' ); ?></span>
				</div>
				<div class="awsm-select-members">
					<?php
					if ( $members->have_posts() ) :
						?>
					<select name="members" id="awsm-members">
						<?php
						echo '<option value="" data-img="' . esc_url( $defaultimage ) . '">' . esc_html_e( 'Select members', 'awsm-team-pro' ) . '</option>';
						while ( $members->have_posts() ) :
							$members->the_post();
							$disabled = '';
							if ( in_array( $members->post->ID, $options['memberlist'] ) ) {
								$disabled = 'disabled';
							}
							echo '<option value="' . esc_attr( $members->post->ID ) . '" data-img="' . esc_url( $this->team_thumbnail( $members->post->ID, 'awsm_team' ) ) . '" ' . esc_attr( $disabled ) . '>' . esc_html( get_the_title() ) . '</option>';
						endwhile;
						wp_reset_postdata();
						?>
					</select>
						<?php
						while ( $members->have_posts() ) :
							$members->the_post();
							echo '<input type="hidden" name="members_list[]" value="' . esc_attr( $members->post->ID ) . '">';
						endwhile;
						?>
						<?php
					else :
						$addmember = admin_url( 'post-new.php?post_type=awsm_team_member' );
						echo '<p>';
						esc_html_e( 'You haven’t added any team members yet. ', 'awsm-team-pro' );
						echo '<a href="' . esc_url( $addmember ) . '">' . esc_html__( 'Add a team member', 'awsm-team-pro' ) . '</a>';
						echo '</p>';
					endif;
					?>
				</div><!-- .awsm-select-members -->
				<ul class="awsm-members-list-selected">
					<div class="awsm-members-info"><?php esc_html_e( 'No Members Selected', 'awsm-team-pro' ); ?></div>
					<script type="text/html" id="tmpl-awsm-member-list">
						<li data-member-id="{{{data.id}}}" class="">
						<img width="31" height="31" src="{{{data.src}}}"/>
						<p>{{{data.title}}}</p><span class="remove-member-to-list" data-member="{{{data.id}}}"><i class="awsm-icon-close"></i></span>
						<input type="hidden" name="memberlist[]" value='{{{data.id}}}'>
						</li>
					</script>
					<?php
					if ( $options['memberlist'] ) :
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
						if ( $team->have_posts() ) :
							while ( $team->have_posts() ) :
								$team->the_post();
								?>
								<li data-member-id="<?php echo esc_attr( $team->post->ID ); ?>" class="">
								<img width="31" height="31" src="<?php echo esc_url( $this->team_thumbnail( $team->post->ID, 'thumbnail' ) ); ?>"/>
								<p><?php the_title(); ?></p><span class="remove-member-to-list" data-member="<?php echo esc_attr( $team->post->ID ); ?>"><i class="awsm-icon-close"></i></span>
								<input type="hidden" name="memberlist[]" value="<?php echo esc_attr( $team->post->ID ); ?>">
								</li>
								<?php
							endwhile;
							wp_reset_postdata();
						endif;
					endif;
					?>
				</ul>
			</div><!-- .awsm-team-customize-member -->
			<div class="awsm-team-member-sorting">
				<div class="awsm-heading-group">
					<h2 class="sub-h"><?php esc_html_e( 'Sorting', 'awsm-team-pro' ); ?> </h2>
				</div>
				<div class="awsm-row">
					<div class="awsm-col-2">
					<select name="awsm_member_order_by" id='awsm_member_order_by' class='awsm-select-default' data-team-id="<?php echo esc_attr( $post->ID ); ?>">
						<option value='drag-and-drop' <?php selected( $options['awsm_member_order_by'], 'drag-and-drop' ); ?>><?php esc_html_e( 'Drag and drop', 'awsm-team-pro' ); ?></option>
						<option value='rand' <?php selected( $options['awsm_member_order_by'], 'rand' ); ?>><?php esc_html_e( 'Random order', 'awsm-team-pro' ); ?></option>
						<option value='title' <?php selected( $options['awsm_member_order_by'], 'title' ); ?>><?php esc_html_e( 'Order by name', 'awsm-team-pro' ); ?></option>
						<option value='date' <?php selected( $options['awsm_member_order_by'], 'date' ); ?>><?php esc_html_e( 'Order by date/time', 'awsm-team-pro' ); ?></option>
					</select>
				</div>
				<div class='awsm-col-2 awsm-columns-wrap'>
					<select name="awsm_member_order" id="awsm_member_order" class='awsm-select-default' style="<?php echo ( $options['awsm_member_order_by'] == 'rand' || $options['awsm_member_order_by'] == 'drag-and-drop' ) ? 'display:none;' : ''; ?>" >
						<option value='asc' <?php selected( $options['awsm_member_order'], 'asc' ); ?>><?php esc_html_e( 'Asc', 'awsm-team-pro' ); ?></option>
						<option value='desc' <?php selected( $options['awsm_member_order'], 'desc' ); ?>><?php esc_html_e( 'Desc', 'awsm-team-pro' ); ?></option>
					</select>
				</div>
				</div>
			</div>
			<div class="awsm-team-customize-style">
				<div class="awsm-heading-group">
					<h2 class="sub-h"><?php esc_html_e( 'Presets', 'awsm-team-pro' ); ?></h2>
					<span><?php esc_html_e( 'Choose a preset from below.', 'awsm-team-pro' ); ?></span>
				</div>
				<div class="awsm-preset-list awsm-clearfix">
							<?php
							$styles = array(
								'Drawer'    => array( 2, 1 ),
								'Modal'     => array( 1, 1 ),
								'Grid'      => array( 4, 1 ),
								'Circles'   => array( 4, 1 ),
								'Cards'     => array( 4, 1 ),
								'List'      => array( 2, 0 ),
								'Table'     => array( 3, 0 ),
								'Slide-Ins' => array( 2, 1 ),
							);
							foreach ( $styles as $key => $set ) :
								$val = strtolower( $key );
								?>
							<input class="awsm-radio-hidden" id="rad-<?php echo esc_attr( $val ); ?>" type="radio" data-style="<?php echo esc_attr( $set[0] ); ?>" data-column="<?php echo esc_attr( $set[1] ); ?>" name="team-style" value="<?php echo esc_attr( $val ); ?>" <?php checked( $val, $options['team-style'] ); ?>>
							<label for="rad-<?php echo esc_attr( $val ); ?>"><img src="<?php echo esc_url( $this->settings['plugin_url'] . '/images/' . $val . '.jpg' ); ?>">
								<span data-type="<?php echo esc_attr( $val ); ?>"><?php echo esc_html( $key ); ?></span>
							</label>
						<?php endforeach; ?>
				</div><!-- .awsm-preset-list -->
				<div class="awsm-section awsm-clearfix">
						<div class="awsm-heading-group">
							<h2 class="sub-h"><?php esc_html_e( 'Style', 'awsm-team-pro' ); ?></h2>
							<span>
							<?php
								$url = 'https://demo.awsm.in/team-pro/';
								printf(
									wp_kses(
										/* translators: %s: Team demo link */
										__( 'We have a set of predefined styles for each preset. Choose your favorite. Refer <a href="%s" target="_blank">demo</a>.', 'awsm-team-pro' ),
										array(
											'a' => array(
												'href'   => array(),
												'target' => array(),
											),
										)
									),
									esc_url( $url )
								);
								?>
								</span>
						</div><!-- .awsm-heading-group -->
						<div class="awsm-row">
							<div class="awsm-col-2">
								<?php
								$preset = array(
									'style-1' => 'Style 1',
									'style-2' => 'Style 2',
									'style-3' => 'Style 3',
									'style-4' => 'Style 4',
								);
								$this->selectbuilder( 'preset', $preset, $options['preset'], '', 'awsm-select-default dyn-sel awsm-styles', 'key' );
								?>
							</div><!-- .awsm-col-2 -->
							<div class="awsm-col-2 awsm-columns-wrap">
								<?php
								$columns = array(
									'2' => '2 Column',
									'3' => '3 Column',
									'4' => '4 Column',
									'5' => '5 Column',
								);
								$this->selectbuilder( 'columns', $columns, $options['columns'], '', 'awsm-select-default dyn-sel awsm-columns', 'key' );
								?>
							</div><!-- .awsm-col-2 -->
						</div><!-- .awsm-row -->
				</div><!-- .awsm-row -->
				<div class="awsm-section awsm-clearfix">
					<div class="awsm-filter-wrap">
						<div class="awsm-heading-group">
							<label><input type="checkbox" name="enable_filter" class="enable_filter" value="1" <?php checked( $options['enable_filter'], '1', true ); ?>><?php esc_html_e( 'Enable filters', 'awsm-team-pro' ); ?></label>
							<p></p>
							<span><?php esc_html_e( 'Check this option to activate filters in the team', 'awsm-team-pro' ); ?></span>
						</div><!-- .awsm-heading-group -->

						<div class="awsm-filter-show<?php echo $options['enable_filter'] && $options['enable_filter'] == 1 ? ' show' : ''; ?>">
							<span> <?php esc_html_e( 'Select the filters and drag and drop to reorder them', 'awsm-team-pro' ); ?></span>
							<p></p>
							<?php
							$current_filters  = get_post_meta( $post->ID, 'team_filters', true );
							$custom_terms_arr = $this->get_ordered_team_filters( $current_filters );
							if ( ! empty( $custom_terms_arr ) ) {
									echo "<select name='team_filters[]' class='awsm-sort-filter' style='width:100%' multiple>";
								foreach ( $custom_terms_arr as $term_id => $term_details ) {
									$extra_attrs = selected( $term_details['selected'], true, false );
									if ( $term_details['disabled'] ) {
										$extra_attrs .= ' disabled';
									}
									// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
									printf( '<option value="%1$s"%3$s>%2$s</option>', esc_attr( $term_id ), esc_html( $term_details['name'] ), $extra_attrs );
								}
								echo '</select>';
							}
							?>

						</div><!-- .awsm-filter-show -->
					</div><!-- .awsm-filter-wrap -->
				</div>
				<div class="awsm-custom-css-wrap">
					<div class="awsm-heading-group">
						<h2 class="sub-h"><?php esc_html_e( 'Custom CSS', 'awsm-team-pro' ); ?></h2>
						<span><?php esc_html_e( 'Want to add your own colours and flavours? Add your custom CSS in the text box below.', 'awsm-team-pro' ); ?></span>
					</div><!-- .awsm-heading-group -->
					<textarea name="custom_css"><?php echo esc_textarea( $options['custom_css'] ); ?></textarea>
				</div>
			</div><!-- .awsm-team-customize-style -->
			<div class="awsm-clearfix"></div>
		</div><!-- .awsm-team-customize-inner -->
	</div><!-- .awsm-team-customize -->
</div><!-- wrap -->
<script type="text/html" id="tmpl-awsm-member-select">
	<div class="select2-result-repository clearfix">
		<# if ( data.src ) { #>
		<div class="awsm-member-thumb">
		<img class="select2-result-repository__avatar" width="150" height="150" src="{{{data.src}}}" />
			</div>
		<# } #>
		<p class="select2-result-repository__title">{{{data.title}}}</p>

   </div>
</script>
