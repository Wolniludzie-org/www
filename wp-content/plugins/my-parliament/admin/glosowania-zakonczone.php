<?php

if(!class_exists('WP_List_Table')){
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
if (!empty($_GET['action'])) {
	$action=sanitize_key($_GET['action']);
}
else {
	$action='';
}
if(!$action) {
	require_once( MYP_PATH . 'admin/class-GlosowaniaZakonczone.php');
	$glosowania = new GlosowaniaZakonczone();
	$glosowania->prepare_items();
	?>
	<div class="wrap">
		<h2>Głosowania zakończone</h2>
		<!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
		<form id="movies-filter" method="get">
			<!-- For plugins, we also need to ensure that the form posts back to our current page -->
			<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
			<!-- Now we can render the completed list table -->
			<?php $glosowania->display() ?>
		</form>
	</div>
	<?php	
}
elseif($action=='details')  {
	require_once( MYP_PATH . 'admin/class-GlosowaniaSzczegoly.php' );
	$pollid=sanitize_key($_GET['poll']);
	global $wpdb;
	$question=$wpdb->get_results( "SELECT question, added, COALESCE( NULLIF( end,0),UNIX_TIMESTAMP()) end  FROM $wpdb->democracy_q where id=$pollid" );
	$glosowania = new GlosowaniaSzczegoly();
	$glosowania->poll_id=$pollid;
	$glosowania->prepare_items();
	?>
	<div class="wrap">
		<h2>Głosowanie nr <?php echo $pollid ?> - <?php echo $question[0]->question ?> | <a href="javascript:history.back();">Wróć do listy</a></h2>
		<form id="movies-filter" method="get">
			<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
			<?php $glosowania->display();
			?>
		</form>
	</div>
	<?php	
	//SELECT count(*) FROM $wpdb->users u WHERE `user_status`=0 AND UNIX_TIMESTAMP(`user_registered`) BETWEEN ".$question[0]->added." AND ".$question[0]->end
	$uprawnieni=$wpdb->get_var("
	SELECT count(*) FROM $wpdb->users u JOIN $wpdb->usermeta um
	ON u.ID = um.user_id
	WHERE `user_status`=0 AND UNIX_TIMESTAMP(`user_registered`) <= ".$question[0]->end."
	AND um.meta_key = 'wl_capabilities' 
	AND (um.meta_value LIKE '%author%' or um.meta_value LIKE '%editor%')
	");
	?>
	<h4>Liczba użytkowników uprawnionych do głosowania: <?php echo $uprawnieni ?></h4>
	<?php
	
}
elseif($action=='uservotes')  {
	require_once( MYP_PATH . 'admin/class-GlosowaniaUzytkownika.php' );
	$userid=sanitize_key($_GET['user']);
	$glosowania = new GlosowaniaSzczegoly();
	$glosowania->userid_id=$userid;
	$glosowania->prepare_items();
	?>
	<div class="wrap">
		<h2>Głosowania użytkownika <?php echo bp_core_get_userlink($userid)?> | <a href="javascript:history.back();">Wróć do listy</a></h2></h2>
		<form id="movies-filter" method="get">
			<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
			<?php 
			$glosowania->display();
			?>
		</form>
	</div>
	<?php
}
elseif($action=='usersvoted')  {
	require_once( MYP_PATH . 'admin/class-GlosowaniaOdpowiedz.php' );
	$pollid=sanitize_key($_GET['poll']);
	$answerid=sanitize_key($_GET['answer']);
	global $wpdb;
	$answer=$wpdb->get_results( "SELECT question, answer FROM $wpdb->democracy_q q join $wpdb->democracy_a a on (q.id=a.qid) where q.id=$pollid and a.aid=$answerid");
	$glosowania = new GlosowaniaOdpowiedz();
	$glosowania->poll_id=$pollid;
	$glosowania->answer_id=$answerid;
	$glosowania->prepare_items();
	?>
	<div class="wrap">
		<h2>Głosowanie nr <?php echo $pollid ?> - <?php echo $answer[0]->question ?> | <a href="javascript:history.back();">Wróć do listy</a></h2>
		<h3>Lista głosujących na odpowiedź: "<?php echo $answer[0]->answer ?>"</h3>
		<form id="movies-filter" method="get">
			<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
			<?php $glosowania->display();
			?>
		</form>
	</div>
	<?php
}
else {
	echo "Nic tu nie ma";
}








