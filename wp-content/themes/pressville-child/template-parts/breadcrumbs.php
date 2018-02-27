<?php $breadcrumbs = lsvr_pressville_get_breadcrumbs();
if ( ! empty( $breadcrumbs ) && count( $breadcrumbs ) > 1 ) : ?>

	<?php do_action( 'lsvr_pressville_breadcrumbs_before' ); ?>

	<!-- BREADCRUMBS : begin -->
	<div id="breadcrumbs">

		<div class="breadcrumbs__inner">
			<div class="lsvr-container">

				<?php if ( true === lsvr_pressville_has_narrow_content() ) : ?>
					<div class="lsvr-grid">
						<div class="lsvr-grid__col lsvr-grid__col--xlg-span-8 lsvr-grid__col--xlg-push-2">
				<?php endif; ?>

				<ul class="breadcrumbs__list">
					<?php foreach ( $breadcrumbs as $breadcrumb ) : ?>
						<li class="breadcrumbs__item">
							<a href="<?php echo esc_url( $breadcrumb['url'] ); ?>" class="breadcrumbs__link"><?php echo esc_html( $breadcrumb['label'] ); ?></a>
						</li>
					<?php endforeach; ?>
				</ul>

				<?php if ( true === lsvr_pressville_has_narrow_content() ) : ?>
						</div>
					</div>
				<?php endif; ?>

			</div>
		</div>

	</div>
	<!-- BREADCRUMBS : end -->

	<?php do_action( 'lsvr_pressville_breadcrumbs_after' ); ?>

<?php endif; ?>