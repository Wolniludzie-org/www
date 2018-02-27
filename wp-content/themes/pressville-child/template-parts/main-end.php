				</div>
			</main>
			<!-- MAIN : end -->

			<?php if ( true === lsvr_pressville_has_narrow_content() ) : ?>
					</div>
				</div>
			<?php endif; ?>

			<?php if ( 'disable' !== lsvr_pressville_get_page_sidebar_position() ) : ?>
				</div>

				<?php if ( 'left' === lsvr_pressville_get_page_sidebar_position() ) : ?>
					<div class="columns__sidebar columns__sidebar--left lsvr-grid__col lsvr-grid__col--span-4 lsvr-grid__col--pull-8">
				<?php else : ?>
					<div class="columns__sidebar columns__sidebar--right lsvr-grid__col lsvr-grid__col--span-4">
				<?php endif; ?>

					<?php // SIDEBAR
					get_sidebar(); ?>

				</div>
			</div>
			<?php endif; ?>

		</div>
	</div>
</div>
<!-- COLUMNS : end -->