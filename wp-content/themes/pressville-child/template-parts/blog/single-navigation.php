<?php $prev_post = get_adjacent_post( false, '', false ); ?>
<?php $next_post = get_adjacent_post( false, '', true ); ?>

<?php if ( true === get_theme_mod( 'blog_single_post_navigation_enable', true ) &&
	( ! empty( $prev_post ) || ! empty( $next_post ) ) ) : ?>

	<!-- POST NAVIGATION : begin -->
	<ul class="post-navigation">

		<?php if ( ! empty( $prev_post ) ) : ?>
			<!-- PREVIOUS POST : begin -->
			<li class="post-navigation__prev">
				<div class="post-navigation__prev-inner">
					<h6 class="post-navigation__title">
						<a href="<?php echo esc_url( get_permalink( $prev_post->ID ) ); ?>"
							class="post-navigation__title-link">
							<?php esc_html_e( 'Previous', 'pressville' ); ?>
						</a>
					</h6>
					<a href="<?php echo esc_url( get_permalink( $prev_post->ID ) ); ?>"
						class="post-navigation__link">
						<?php echo esc_html( $prev_post->post_title ); ?>
					</a>
				</div>
			</li>
			<!-- PREVIOUS POST : end -->
		<?php endif; ?>

		<?php if ( ! empty( $next_post ) ) : ?>
			<!-- NEXT POST : begin -->
			<li class="post-navigation__next">
				<div class="post-navigation__next-inner">
					<h6 class="post-navigation__title">
						<a href="<?php echo esc_url( get_permalink( $next_post->ID ) ); ?>"
							class="post-navigation__title-link">
							<?php esc_html_e( 'Next', 'pressville' ); ?>
						</a>
					</h6>
					<a href="<?php echo esc_url( get_permalink( $next_post->ID ) ); ?>"
						class="post-navigation__link">
						<?php echo esc_html( $next_post->post_title ); ?>
					</a>
				</div>
			</li>
			<!-- NEXT POST : end -->
		<?php endif; ?>

	</ul>
	<!-- POST NAVIGATION : end -->

<?php endif; ?>