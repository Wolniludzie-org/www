<?php

if(!class_exists('WP_List_Table')){
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
	require_once( MYP_PATH . 'admin/class-GlosowaniaOtwarte.php' );
	$glosowania = new GlosowaniaOtwarte();
	$glosowania->prepare_items();
	?>
	<div class="wrap">
		<h2>Głosowania otwarte</h2>
		<!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
		<form id="movies-filter" method="get">
			<!-- For plugins, we also need to ensure that the form posts back to our current page -->
			<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
			<!-- Now we can render the completed list table -->
			<?php $glosowania->display() ?>
		</form>
	</div>
	<?php	
