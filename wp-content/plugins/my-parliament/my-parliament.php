<?php
/*
Plugin Name:  My Parliament
Description:  Rozszerzenie do democracy-poll
Version:      20180131
Author:       jstepa@bestgo.pl
License:      GPL2
License URI:  https://www.gnu.org/licenses/gpl-2.0.html
Text Domain:  my-parliament
Domain Path:  /languages
*/
defined( 'ABSPATH' ) or die( 'No direct access!' );
define('MYP_MAIN_FILE',  __FILE__ );
define('MYP_URL',  plugin_dir_url(MYP_MAIN_FILE) );
define('MYP_PATH', plugin_dir_path(MYP_MAIN_FILE) );

add_action('admin_menu', 'myp_setup_menu');

function myp_setup_menu(){
        add_menu_page( 'Nasze głosowania', 'Nasze głosowania', 'edit_posts', 'myp');
        add_submenu_page( 'myp', 'Głosowania zakończone', 'Głosowania zakończ.', 'edit_posts', 'myp_glosowania-zakonczone','myp_zakonczone');
        add_submenu_page( 'myp', 'Głosowania otwarte', 'Głosowania otwarte', 'edit_posts', 'myp_glosowania-otwarte','myp_otwarte');
		remove_submenu_page('myp','myp');
}
	
function myp_zakonczone(){
 include('admin/glosowania-zakonczone.php');
}
function myp_otwarte(){
 include('admin/glosowania-otwarte.php');
}

add_action('admin_enqueue_scripts','myp_scripts');

function myp_scripts(){
	wp_enqueue_style( 'mypstyle', plugins_url( 'css/myp.css' , __FILE__ ) );
}