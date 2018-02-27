<!-- POST ARCHIVE : begin -->
<div class="lsvr_event-post-page post-archive lsvr_event-post-archive lsvr_event-post-archive--default">
	<?php // ARCHIVE HEADER
	//get_template_part( 'template-parts/blog/archive-header' ); 
	?>
	
	<?php if ( have_posts() ) : ?>
	
	<div class="post-archive__grid">
		<div class="lsvr-grid lsvr-grid--2-cols lsvr-grid--md-2-cols lsvr-grid--sm-2-cols">
		
	
		<?php while ( have_posts() ) : the_post(); ?>
			<div class="lsvr-grid__col lsvr-grid__col--span-6 lsvr-grid__col--md-span-6 lsvr-grid__col--sm-span-6">
			<!-- POST : begin -->
			<!-- <article <?php post_class(); ?>> -->
			<article class="post lsvr_event has-post-thumbnail">
			<div class="post__inner">
				<header class="post__header">
				<?php lsvr_pressville_the_event_post_archive_thumbnail(get_the_ID()); ?>
				</header>
				<!-- POST CONTENT : begin -->
				<div class="post__content">
					<h3 class="post__title">
						<a href="<?php the_permalink(); ?>" class="post__title-link" rel="bookmark"><?php the_title(); ?></a>
					</h3>					
					<?php if ( ! empty( $post->post_excerpt ) ) : ?>
						<?php the_excerpt(); ?>

						<p class="post__permalink">
							<a href="<?php the_permalink(); ?>" class="c-button post__permalink-link" rel="bookmark">
								<?php esc_html_e( 'Read More', 'pressville' ); ?>
							</a>
						</p>

					<?php elseif ( $post->post_content ) : ?>
						<?php the_content(); ?>
					<?php endif; ?>

					<p class="post__meta">
						<time class="post__meta-date" datetime="<?php echo esc_attr( get_the_time( 'c' ) ); ?>"><?php echo get_the_date(); ?></time>
						<span class="post__meta-categories"><?php lsvr_pressville_the_post_categories( get_the_ID(), 'category', esc_html__( 'in %s', 'pressville' ) ); ?></span>
					</p>
				</div>
			</div>
			</article>
			</div>
			<!-- POST : end -->
		<?php endwhile; ?>
		</div>
	</div>
		<?php // Pagination
		the_posts_pagination(); ?>
	<?php endif; ?>
</div>
<!-- POST ARCHIVE : end -->