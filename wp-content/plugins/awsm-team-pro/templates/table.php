<?php
/**
 * Table Preset Template.
 *
 * Override this by copying it to currenttheme/awsm-team-pro/table.php
 *
 * @package awsm-team-pro
 */

?>
<div id="<?php echo esc_attr( $this->add_id( array( 'awsm-team', $id ) ) ); ?>" class="awsm-grid-wrapper">
	<?php echo $this->show_team_filter( $team, $id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

	<?php if ( $team->have_posts() ) : ?>
		<div class="awsm-grid">
			<div class="awsm-table <?php echo esc_attr( $this->item_style( $options ) ); ?>">
				<div class="awsm-table-row awsm-table-head">
					<div class="awsm-table-cell">
						<?php esc_html_e( 'Image', 'awsm-team-pro' ); ?>
					</div>
					<div class="awsm-table-cell">
						<?php esc_html_e( 'Name', 'awsm-team-pro' ); ?>
					</div>
					<div class="awsm-table-cell">
						<?php esc_html_e( 'Designation', 'awsm-team-pro' ); ?>
					</div>
					<div class="awsm-table-cell">
						<?php esc_html_e( 'Short Description', 'awsm-team-pro' ); ?>
					</div>
					<div class="awsm-table-cell">
						<?php esc_html_e( 'Social Links', 'awsm-team-pro' ); ?>
					</div>
				</div>
				<?php
				while ( $team->have_posts() ) :
					$team->the_post();
					$teamdata     = $this->get_options( 'awsm_team_member', $team->post->ID );
					$member_terms = 'awsm-all';
					$terms        = get_the_terms( $team->post->ID, 'awsm_team_filters' );
					if ( ! empty( $terms ) ) {
						foreach ( $terms as $member_term ) {
							$member_terms .= ' awsm-' . str_replace( ' ', '-', $member_term->term_id );
						}
					}
					?>
				<div id="<?php echo esc_attr( $this->add_id( array( 'awsm-member', $id, $team->post->ID ) ) ); ?>" class="awsm-table-row awsm-team-item awsm-scale-anm <?php echo esc_attr( $member_terms ); ?>">
					<div class="awsm-table-cell awsm-table-image">
						<div class="awsm-table-img-holder">
							<?php echo $this->get_team_thumbnail( $team->post->ID ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</div>
					</div>
					<div class="awsm-table-cell awsm-table-name">
						<div class="awsm-table-cell-inner"><?php the_title(); ?></div>
					</div>
					<div class="awsm-table-cell awsm-table-designation">
						<div class="awsm-table-cell-inner"><?php $this->checkprint( '%s', wp_kses( $teamdata['awsm-team-designation'], 'post' ) ); ?></div>
					</div>
					<div class="awsm-table-cell awsm-table-description">
						<div class="awsm-table-cell-inner"><?php $this->checkprint( '<p>%s</p>', wp_kses( $teamdata['awsm-team-short-desc'], 'post' ) ); ?></div>
					</div>
					<div class="awsm-table-cell">
						<?php
						include $this->get_template_path( 'social.php', 'partials' );
						?>
					</div>
				</div>
					<?php
				endwhile;
				wp_reset_postdata();
				?>
			</div>
		</div>
	<?php endif; ?>
</div>
