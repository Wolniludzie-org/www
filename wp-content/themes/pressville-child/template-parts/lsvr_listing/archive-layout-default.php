<?php 
$sort= $_GET['sortowanie'];
if($sort == "nazwa") {$orderby= "title";$order='ASC';}
else if($sort == "data") {$orderby= "date";$order='DESC';}
else {$orderby= "rand";$order='';}
?>
<!-- POST ARCHIVE : begin -->
<div class="lsvr_listing-post-page post-archive lsvr_listing-post-archive lsvr_listing-post-archive--default">

	<?php // ARCHIVE HEADER
	get_template_part( 'template-parts/lsvr_listing/archive-header' ); ?>

	<?php // Directory categories
	if ( true === get_theme_mod( 'lsvr_listing_archive_categories_enable', true ) ) {
		lsvr_pressville_the_post_archive_categories( 'lsvr_listing', 'lsvr_listing_cat' );
	} 
	?>
	<div class="my-sort-box">
	<span>Sortowanie:</span> <a title="Losowo" href="?sortowanie=losowo" <?php if ($sort == "losowo" || empty($sort)){ echo 'class="active"'; } ?>>Losowo</a> | <a title="Nazwa (rosnąco)"  href="?sortowanie=nazwa" <?php if ($sort == "nazwa"){ echo 'class="active"'; } ?>>Nazwa</a> | <a title="Data (malejąco)" href="?sortowanie=data" <?php if ($sort == "data"){ echo 'class="active"'; } ?>>Data</a>
	</div>
	
	<?php if ( have_posts() && empty($sort) ) : 
	//global $wp_query;
	//print_r($wp_query);
	?>
	
		<!-- POST ARCHIVE GRID : begin -->
		<div class="post-archive__grid">
			<div class="<?php lsvr_pressville_the_listing_archive_grid_class(); ?>">

				<?php while ( have_posts() ) : the_post(); ?>

					<div class="<?php lsvr_pressville_the_listing_archive_grid_column_class(); ?>">

						<!-- POST : begin -->
						<article <?php post_class( 'post' ); ?>>
							<div class="post__inner">

								<?php if ( has_post_thumbnail( get_the_ID() ) ) : ?>
								<!-- POST HEADER : begin -->
								<header class="post__header">

									<?php // Post thumbnail
									lsvr_pressville_the_listing_archive_thumbnail( get_the_ID() ); ?>

								</header>
								<!-- POST HEADER : end -->
								<?php endif; ?>

								<!-- POST CONTENT : begin -->
								<div class="post__content">

									<?php if ( lsvr_pressville_has_post_terms( get_the_ID(), 'lsvr_listing_cat' ) ) : ?>

										<!-- POST META : begin -->
										<p class="post__meta">
											<span class="post__meta-categories"><?php lsvr_pressville_the_post_categories( get_the_ID(), 'lsvr_listing_cat', esc_html__( 'in %s', 'pressville' ) ); ?></span>
										</p>
										<!-- POST META : end -->

									<?php endif; ?>

									<!-- POST TITLE : begin -->
									<h2 class="post__title">
										<a href="<?php the_permalink(); ?>" class="post__title-link" rel="bookmark"><?php the_title(); ?></a>
									</h2>
									<!-- POST TITLE : end -->

									<?php if ( lsvr_pressville_has_listing_address( get_the_ID() ) ) : ?>
									<!-- POST ADDRESS : begin -->
									<p class="post__address">
										<?php lsvr_pressville_the_listing_address( get_the_ID() ); ?>
									</p>
									<!-- POST ADDRESS : end -->
									<?php endif; ?>

								</div>
								<!-- POST CONTENT : end -->

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

	<?php elseif ( have_posts() && !empty($sort) ) : ?>
		<div class="post-archive__grid">
			<div class="<?php lsvr_pressville_the_listing_archive_grid_class(); ?>">
	<?php 
	$slug=get_queried_object()->slug;
	$loop = new WP_Query(array('post_type'=>'lsvr_listing','taxonomy'=>'lsvr_listing_cat','term'=>$slug,'orderby'=>$orderby,'order'=>$order));

		while ( $loop->have_posts() ) : $loop->the_post(); ?>

					<div class="<?php lsvr_pressville_the_listing_archive_grid_column_class(); ?>">

						<!-- POST : begin -->
						<article <?php post_class( 'post' ); ?>>
							<div class="post__inner">

								<?php if ( has_post_thumbnail( get_the_ID() ) ) : ?>
								<!-- POST HEADER : begin -->
								<header class="post__header">

									<?php // Post thumbnail
									lsvr_pressville_the_listing_archive_thumbnail( get_the_ID() ); ?>

								</header>
								<!-- POST HEADER : end -->
								<?php endif; ?>

								<!-- POST CONTENT : begin -->
								<div class="post__content">

									<?php if ( lsvr_pressville_has_post_terms( get_the_ID(), 'lsvr_listing_cat' ) ) : ?>

										<!-- POST META : begin -->
										<p class="post__meta">
											<span class="post__meta-categories"><?php lsvr_pressville_the_post_categories( get_the_ID(), 'lsvr_listing_cat', esc_html__( 'in %s', 'pressville' ) ); ?></span>
										</p>
										<!-- POST META : end -->

									<?php endif; ?>

									<!-- POST TITLE : begin -->
									<h2 class="post__title">
										<a href="<?php the_permalink(); ?>" class="post__title-link" rel="bookmark"><?php the_title(); ?></a>
									</h2>
									<!-- POST TITLE : end -->

									<?php if ( lsvr_pressville_has_listing_address( get_the_ID() ) ) : ?>
									<!-- POST ADDRESS : begin -->
									<p class="post__address">
										<?php lsvr_pressville_the_listing_address( get_the_ID() ); ?>
									</p>
									<!-- POST ADDRESS : end -->
									<?php endif; ?>

								</div>
								<!-- POST CONTENT : end -->

							</div>
						</article>
						<!-- POST : end -->

					</div>		
		
	<?php endwhile; wp_reset_query(); ?>
	</div>
	</div>
	<?php else : ?>
		<?php lsvr_pressville_the_alert_message( esc_html__( 'No listings matched your criteria', 'pressville' ) ); ?>
	<?php endif; ?>

</div>
<!-- POST ARCHIVE : end -->