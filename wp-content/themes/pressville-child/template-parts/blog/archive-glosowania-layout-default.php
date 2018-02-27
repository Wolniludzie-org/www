<!-- POST ARCHIVE : begin -->
<div class="post-archive blog-post-archive blog-post-archive--grid">

	<?php // ARCHIVE HEADER
	get_template_part( 'template-parts/blog/archive-glosowania-header' ); ?>

	<?php if ( have_posts() ) : ?>

		<!-- POST ARCHIVE GRID : begin -->
		<div class="post-archive__grid">
			<div class="<?php lsvr_pressville_the_blog_post_archive_grid_class(); ?>">

				<?php while ( have_posts() ) : the_post(); ?>

					<!-- <div class="<?php lsvr_pressville_the_blog_post_archive_grid_column_class( get_the_ID() ); ?>"> -->
					<div class="lsvr-grid__col lsvr-grid__col--span-3 lsvr-grid__col--lg-span-4 lsvr-grid__col--md-span-6">

						<!-- POST : begin -->
						<article <?php post_class("post"); ?>
							<?php lsvr_pressville_the_blog_post_background_thumbnail( get_the_ID() ); ?>>
							<div class="post__inner">
								<div class="post__bg">

									<!-- POST HEADER : begin -->
									<header class="post__header">

										<!-- POST TITLE : begin -->
										<h2 class="post__title">
											<a href="<?php the_permalink(); ?>" class="post__title-link" rel="bookmark"><?php the_title(); ?></a>
										</h2>
										<!-- POST TITLE : end -->

										<!-- POST META : begin -->
										<p class="post__meta">
											<time class="post__meta-date" datetime="<?php echo esc_attr( get_the_time( 'c' ) ); ?>"><?php echo get_the_date(); ?></time>
											<span class="post__meta-categories"><?php lsvr_pressville_the_post_categories( get_the_ID(), 'category', esc_html__( 'in %s', 'pressville' ) ); ?></span>
										</p>
										<!-- POST META : end -->

									</header>
									<!-- POST HEADER : end -->

									<!-- OVERLAY LINK : begin -->
									<a href="<?php the_permalink(); ?>"
										class="post__overlay-link">
										<span class="screen-reader-text"><?php esc_html_e( 'Read More', 'pressville' ); ?></span>
									</a>
									<!-- OVERLAY LINK : end -->

								</div>
							</div>
						</article>
						<!-- POST : end -->

					</div>

				<?php endwhile; ?>

			</div>
		</div>
		<!-- POST ARCHIVE GRID : end -->

		<?php // PAGINATION
		the_posts_pagination(); ?>

	<?php endif; ?>

</div>
<!-- POST ARCHIVE : end -->