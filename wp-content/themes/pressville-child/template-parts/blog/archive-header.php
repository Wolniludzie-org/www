<!-- MAIN HEADER : begin -->
<header class="main__header">
	<h1 class="main__title is-main-headline">
		<?php if ( is_tax() ) : ?>
			<?php single_term_title(); ?>
		<?php else : ?>
			<?php echo lsvr_pressville_get_blog_archive_title(); ?>
		<?php endif; ?>
	</h1>
</header>
<!-- MAIN HEADER : end -->