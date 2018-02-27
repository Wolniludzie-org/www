<?php get_header(); ?>

<?php // BREADCRUMBS
get_template_part( 'template-parts/breadcrumbs' ); ?>

<?php // MAIN START
get_template_part( 'template-parts/main', 'begin' ); ?>

<?php // ARCHIVE TEMPLATE
get_template_part( 'template-parts/blog/archive-glosowania-layout', lsvr_pressville_get_blog_archive_layout() ); ?>

<?php // MAIN END
get_template_part( 'template-parts/main', 'end' ); ?>

<?php get_footer(); ?>