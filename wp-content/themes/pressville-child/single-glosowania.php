<?php get_header(); ?>

<?php // BREADCRUMBS
get_template_part( 'template-parts/breadcrumbs' ); ?>

<?php // MAIN START
get_template_part( 'template-parts/main', 'begin' ); ?>

<!-- POST SINGLE : begin -->
<div class="post-single blog-post-single">

	<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

		<!-- POST : begin -->
		<article <?php post_class(); ?>>
			<div class="post__inner">

				<!-- POST HEADER : begin -->
				<header class="post__header">

					<!-- POST TITLE : begin -->
					<h1 class="post__title is-main-headline"><?php the_title(); ?></h1>
					<!-- POST TITLE : end -->

					<?php if ( true === get_theme_mod( 'blog_single_author_enable', true ) || lsvr_pressville_has_post_terms( get_the_ID(), 'category' ) ) : ?>

						<!-- POST META : begin -->
						<p class="post__meta">

							<time class="post__meta-date" datetime="<?php echo esc_attr( get_the_time( 'c' ) ); ?>"><?php the_date(); ?></time>

							<?php if ( true === get_theme_mod( 'blog_single_author_enable', true ) ) : ?>
								<span class="post__meta-author"><?php echo sprintf( esc_html__( 'by %s', 'pressville' ), '<a href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '" class="post__meta-author-link" rel="author">' . get_the_author() . '</a>' ); ?></span>
							<?php endif; ?>

							<?php if ( lsvr_pressville_has_post_terms( get_the_ID(), 'category' ) ) : ?>
								<span class="post__meta-categories"><?php lsvr_pressville_the_post_categories( get_the_ID(), 'category', esc_html__( 'in %s', 'pressville' ) ); ?></span>
							<?php endif; ?>

						</p>
						<!-- POST META : end -->

					<?php endif; ?>

				</header>
				<!-- POST HEADER : end -->

				<?php if ( has_post_thumbnail() ) : ?>
				<!-- POST THUMBNAIL : begin -->
				<p class="post__thumbnail">
					<?php the_post_thumbnail( 'thumb' ); ?>
				</p>
				<!-- POST THUMBNAIL : end -->
				<?php endif; ?>

				<!-- POST CONTENT : begin -->
				<div class="post__content">
					<?php the_content(); ?>
					<?php wp_link_pages(); ?>
				</div>
				<!-- POST CONTENT : end -->

				<?php if ( has_tag() ) : ?>
				<!-- POST FOOTER : begin -->
				<footer class="post__footer">

					<!-- POST TAGS : begin -->
					<!-- We are using get_the_terms() function instead of simple the_tags() for consistency with other post types -->
					<div class="post__tags">
						<h6 class="screen-reader-text"><?php esc_html_e( 'Tags:', 'pressville' ); ?></h6>
						<?php lsvr_pressville_the_post_tags( get_the_ID(), 'post_tag' ); ?>
					</div>
					<!-- POST TAGS : end -->

				</footer>
				<!-- POST FOOTER : end -->
				<?php endif; ?>

				<?php // Add custom code at post bottom
				do_action( 'lsvr_pressville_blog_single_bottom' ); ?>

			</div>
		</article>
		<!-- POST : end -->

		<?php // POST NAVIGATION
		get_template_part( 'template-parts/blog/single-navigation' ); ?>

	    <?php // POST COMMENTS
	    comments_template(); ?>

	<?php endwhile; endif; ?>

</div>
<!-- POST SINGLE : end -->

<?php // MAIN END
get_template_part( 'template-parts/main', 'end' ); ?>

<?php get_footer(); ?>