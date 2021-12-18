<?php
/**
 * Drawer Preset Template.
 *
 * Override this by copying it to currenttheme/awsm-team-pro/drawer.php
 *
 * @package awsm-team-pro
 */

?>
<div id="<?php echo esc_attr( $this->add_id( array( 'awsm-team', $id ) ) ); ?>" class="awsm-grid-wrapper">
	<?php echo $this->show_team_filter( $team, $id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

	<?php if ( $team->have_posts() ) : ?>
	<div class=''>
		<div class="gridder awsm-grid <?php echo esc_attr( $this->item_style( $options ) ); ?>">
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
				<div id="<?php echo esc_attr( $this->add_id( array( 'awsm-member', $id, $team->post->ID ) ) ); ?>" class="awsm-grid-list awsm-grid-card awsm-team-item awsm-scale-anm <?php echo esc_attr( $member_terms ); ?>" data-griddercontent="#awsm-grid-content-<?php echo esc_attr( $team->post->ID ); ?>">
					<span class="awsm-team-link-control awsm-grid-list-item awsm-grid-list-item-<?php echo esc_attr( $team->post->ID ); ?>"<?php Awsm_Team::deep_link_attr( $team->post->ID ); ?>>
						<figure>
						<?php echo $this->get_team_thumbnail( $team->post->ID ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							<figcaption>
								<div class="awsm-personal-info">
								<?php $this->checkprint( '<span>%s</span>', wp_kses( $teamdata['awsm-team-designation'], 'post' ) ); ?>
									<h3><?php the_title(); ?></h3>
								</div>
							</figcaption>
						</figure>
					</span>
				</div>
			<?php
			endwhile;
		wp_reset_postdata();
		?>
		</div>
		<div class="awsm-grid-expander style-1">
			<?php
			while ( $team->have_posts() ) :
				$team->the_post();
				$teamdata = $this->get_options( 'awsm_team_member', $team->post->ID );
				?>
			<div id="<?php echo esc_attr( $this->add_id( array( 'awsm-member-info', $id, $team->post->ID ) ) ); ?>"  class="awsm-grid-expander <?php echo esc_attr( $options['preset'] ); ?>">
				<div class="awsm-detailed-info" id="awsm-grid-content-<?php echo esc_attr( $team->post->ID ); ?>">
					<div class="awsm-details">
						<div class="awsm-personal-details">
							<div class="awsm-content-scrollbar">
								<?php
								$this->checkprint( '<span>%s</span>', wp_kses( $teamdata['awsm-team-designation'], 'post' ) );
								the_title( '<h2>', '</h2>' );
								the_content();
								?>
							</div>
						</div>
					</div>
					<div class="awsm-personal-contact-info">
					   <?php
						include $this->get_template_path( 'contact.php', 'partials' );
						include $this->get_template_path( 'social.php', 'partials' );
						?>
					</div>
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
