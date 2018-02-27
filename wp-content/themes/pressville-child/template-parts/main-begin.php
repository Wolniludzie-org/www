<!-- COLUMNS : begin -->
<div id="columns">
	<div class="columns__inner">
		<div class="lsvr-container">

			<?php if ( 'left' === lsvr_pressville_get_page_sidebar_position() ) : ?>
				<div class="lsvr-grid">
					<div class="columns__main lsvr-grid__col lsvr-grid__col--span-8 lsvr-grid__col--push-4">
			<?php elseif ( 'right' === lsvr_pressville_get_page_sidebar_position() ) : ?>
				<div class="lsvr-grid">
					<div class="columns__main lsvr-grid__col lsvr-grid__col--span-8">
			<?php endif; ?>

			<?php if ( true === lsvr_pressville_has_narrow_content() ) : ?>
				<div class="lsvr-grid">
					<div class="lsvr-grid__col lsvr-grid__col--xlg-span-8 lsvr-grid__col--xlg-push-2">
			<?php endif; ?>

			<!-- MAIN : begin -->
			<main id="main">
				<div class="main__inner">