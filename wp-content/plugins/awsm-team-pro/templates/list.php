<?php
/**
 * List Preset Template.
 *
 * Override this by copying it to currenttheme/awsm-team-pro/list.php
 *
 * @package awsm-team-pro
 */

?>
<div id="<?php echo esc_attr( $this->add_id( array( 'awsm-team', $id ) ) ); ?>" class="awsm-grid-wrapper">
	<?php echo $this->show_team_filter( $team, $id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

	<?php if ( $team->have_posts() ) : ?>
		<div class="awsm-grid <?php echo esc_attr( $this->item_style( $options ) ); ?>">
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
				<div id="<?php echo esc_attr( $this->add_id( array( 'awsm-member', $id, $team->post->ID ) ) ); ?>" class="awsm-grid-card awsm-team-item awsm-scale-anm <?php echo esc_attr( $member_terms ); ?>">
				   <figure>
					 <?php echo $this->get_team_thumbnail( $team->post->ID ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						 <figcaption>
							<div class="awsm-personal-info">
							   <h3><?php the_title(); ?></h3>
							   <?php $this->checkprint( '<span>%s</span>', wp_kses( $teamdata['awsm-team-designation'], 'post' ) ); ?>
							</div>
							<div class="awsm-contact-info">
							<?php
							the_content();
							include $this->get_template_path( 'contact.php', 'partials' );
							include $this->get_template_path( 'social.php', 'partials' );
							?>
							</div>
						 </figcaption>
				   </figure>
				</div>
			<?php
			endwhile;
		wp_reset_postdata();
		?>
		</div>
		<?php endif; ?>
</div>
