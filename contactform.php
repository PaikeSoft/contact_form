<?php
/*
Plugin Name: Contact form
Plugin URI: http://paike.s-host.net
Description: Create contact form for Wordpress
Version: 1.0.0
Author: vok
Author URL: http://paike.s-host.net
*/


//Function activate plugin
register_activation_hook( __FILE__, 'pcf_activate' );
 
//Function deactivate plugin
register_deactivation_hook( __FILE__, 'pcf_deactivate' );
 
//Function uninstall plugin
register_uninstall_hook( __FILE__, 'pcf_uninstall' );

add_action('wp_enqueue_script', 'pcf_load_scripts');

//Add scripts to backend
add_action('admin_enqueue_scripts', 'pcfa_load_scripts');

//Create admin menu
add_action('admin_menu', 'CreatePluginMenuPcf');


/*------------------------- create menu -------------------------*/
function CreatePluginMenuPcf()
{
    if (function_exists('add_options_page'))
    {
		add_menu_page( __( 'Contact form', 'paike_contactform' ), __( 'Contact form', 'paike_contactform' ), 'manage_options', 'paike_contactform', 'contactform', plugins_url( 'contactform/images/letter.png' ), 6);
		add_submenu_page( 'paike_contactform', __( 'Messages', 'paike_contactform' ), __( 'Messages', 'paike_contactform' ), 'manage_options', 'cf_messages', 'messagesPageOptions');
    }
}


/*------------------------- Site page -------------------------*/
add_shortcode('paike_contactform', 'site_contactform');
/*
 * show contact page oon frontend
*/function site_contactform ()
{
	global $wpdb;

	$arr_field = $wpdb->get_results('SELECT * FROM `'.$wpdb->prefix.'cform_fields` WHERE `show`="1"');
	$settings = $wpdb->get_results('SELECT * FROM `'.$wpdb->prefix.'cform_settings`');

	foreach ($settings as $k => $v) {
		$arr_settings[$v->name] = $v->value;
	}

	include 'templates/contactform.php';
}


/*------------------------- Plugin admin page -------------------------*/
/*
 * get main parameters of plugin: input name, show or hide input
*/
function contactform()
{
	global $wpdb;

	$sql = '';
	if ( isset($_REQUEST['submit']) ) {
		//save fields
		foreach ($_REQUEST['field'] as $k=>$v){
			$set = '`placeholder`="'.$v['placeholder'].'", `show`="'.$v['show'].'", `required`="'.$v['required'].'"';
			$wpdb->query('UPDATE `'.$wpdb->prefix.'cform_fields` SET '.$set.' WHERE `name`="'.$k.'"');
		}

		//save settings
		foreach ($_REQUEST['setting'] as $k=>$v){
			$wpdb->query('UPDATE `'.$wpdb->prefix.'cform_settings` SET `value`="'.$v.'" WHERE `name`="'.$k.'"'); 
		}
		if (!isset($_REQUEST['setting']['save_messages'])) {
			echo 'not set';
			$wpdb->query('UPDATE `'.$wpdb->prefix.'cform_settings` SET `value`="0" WHERE `name`="save_messages"'); 
		}
	}

	$arr_field = $wpdb->get_results('SELECT * FROM `'.$wpdb->prefix.'cform_fields`');
	$arr_settings = $wpdb->get_results('SELECT * FROM `'.$wpdb->prefix.'cform_settings`');

	include 'templates/settingsform.php';
}


/*
 * messages page
*/
function messagesPageOptions()
{
	global $wpdb;

	if ( $_REQUEST['action'] == 'delete' ) {
		$wpdb->query('DELETE FROM `'.$wpdb->prefix.'cform_message` WHERE `id`="'.$_REQUEST['id'].'"');
	}
	$on_page = 10;

	if ( isset($_REQUEST['mpage']) ) {
		$offset = intval($_REQUEST['mpage']) * $on_page;
		$offset_sql = ' OFFSET '.$offset;
		$cur_page = $_REQUEST['mpage'];
	}
	else {
		$offset_sql = '';
		$cur_page = 0;
	}
	
	$ae = $wpdb->get_results('SELECT COUNT(*) as count FROM `'.$wpdb->prefix.'cform_message`');
	$count_pages = ceil($ae[0]->count / $on_page);

	//links to pages
	if ( $count_pages > 0 ) $page_nav = page_navigation($cur_page, '?page=cf_messages&mpage=', 5, $count_pages);

	//list pages on current page
	$arr_messages = $wpdb->get_results('SELECT * FROM `'.$wpdb->prefix.'cform_message` LIMIT '.$on_page.$offset_sql);

	include 'templates/messages.php';
}


/*
 * return html with navigations links
*/
function page_navigation($cur_page, $link, $nav_count, $count_pages)
{
	//return all pages
	if ($nav_count > $count_pages ){
		for ( $i = 0; $i < $count_pages; $i++ ){
			//all pages
			$block[0]['st'] = 0;
			$block[0]['f'] = $count_pages;
		}
	}
	//return start-middle-end link of path
	else {
		//1 block and start 2 block
		if ( ($cur_page - $nav_count) < $nav_count) {
			$block[0]['st'] = -1;
			$block[0]['f'] = -1;

			$block[1]['st'] = 0;
		}
		else {
			$block[0]['st'] = 0;
			$block[0]['f'] = $nav_count;

			$block[1]['st'] = $cur_page - $nav_count;
		}

		//3 block and finish 2 block
		if ( ($cur_page + $nav_count) > ($count_pages - $nav_count)) {
			$block[2]['st'] = -1;
			$block[2]['f'] = -1;

			$block[1]['f'] = $count_pages;
		}
		else {
			$block[2]['st'] = $count_pages - $nav_count;
			$block[2]['f'] = $count_pages;

			$block[1]['f'] = $cur_page + $nav_count;
		}
	}

	$i = 0;
	$html = '<ul class="pcf-breadcrumb">';

	//previous page
	if ( $cur_page > 0 ) {
		$pr_page = $cur_page - 1;
		$html .= '<li><a href="'.$link.$pr_page.'" class="pcf-page">&#x25C4; '.__( 'Previous page', 'paike_contactform' ).'</a><li>';
	}
	else {
		$html .= '<li><span class="pcf-page">&#x25C4; '.__( 'Previous page', 'paike_contactform' ).'</span></li>';		
	}

	//next page
	if ( ($cur_page + 1) < $count_pages ) {
		$next_page = $cur_page + 1;
		$html .= '<li><a href="'.$link.$next_page.'" class="pcf-page">'.__( 'Next page', 'paike_contactform' ).' &#x25BA;</a><li>';
	}
	else {
		$html .= '<li><span class="pcf-page">'.__( 'Next page', 'paike_contactform' ).' &#x25BA;</span></li>';
	}

	foreach ($block as $k=>$v) {
		if ( $v['st'] != $i && $i != 0) $html .= '<li class="pcf-space"><span>...</span></li>';

		if ($v['st'] != -1) {
			for ($i = $v['st']; $i < $v['f']; $i++) {
				$page = $i + 1;
				if ( $i != $cur_page ) {
					$html .= '<li><a href="'.$link.$i.'">'.$page.'</a></li>';
				}
				else {
					$html .= '<li><span>'.$page.'</span></li>';
				}
			}
		}
	}

	$html .= '</ul>';

	return $html;
}

/*
 * Add scripts to frontend
*/
add_action('wp_enqueue_scripts', 'pcf_load_scripts');


/*
 * Add scripts on frontend page
*/
function pcf_load_scripts()
{
	wp_enqueue_style( 'wppcf', plugins_url('css/style.css', __FILE__), array(), WPCMN_VER, 'all' );
	wp_enqueue_script('wppcfjs', plugins_url('js/scripts.js', __FILE__), array(), WPCMN_VER, 'all');
}


/*
 * Add scripts on admin page
*/
function pcfa_load_scripts()
{
	wp_enqueue_style( 'wppcf-admin', plugins_url('css/admin.css', __FILE__), array(), WPCMN_VER, 'all' );
}

function html_chekbox($name, $value){
	if ( $value == 0 ) {$checked0 = 'checked="checked"'; $checked1 = '';}
	else {$checked1 = 'checked="checked"'; $checked0 = '';}

	echo '<div class="checkbox-state">';
	echo '<input type="radio" name="'.$name.'" value="0" '.$checked0.' />';
	echo '<input type="radio" name="'.$name.'" class="checkbox-state-on" value="1" '.$checked1.' />';
	echo '</div>';
}


/*
 * Activate plugin
 */
function pcf_activate()
{
global $wpdb;
require_once(ABSPATH . 'wp-admin/upgrade-functions.php');

//Tables
$sql = 'CREATE TABLE IF NOT EXISTS `'.$wpdb->prefix.'cform_fields` (
`id` int(11) NOT NULL,
`name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
`text` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
`placeholder` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
`show` int(11) NOT NULL,
`required` int(11) NOT NULL,
`type` varchar(11) COLLATE utf8_unicode_ci NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;';
dbDelta($sql);

$sql = 'CREATE TABLE IF NOT EXISTS `'.$wpdb->prefix.'cform_settings` (
`id` int(11) NOT NULL,
`name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
`text` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
`value` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
`type` varchar(11) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;';
dbDelta($sql);

$sql = 'CREATE TABLE IF NOT EXISTS `'.$wpdb->prefix.'cform_message` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`data` varchar(500) NOT NULL,
`message` mediumtext,
`date` int(20) NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_unicode_ci';
dbDelta($sql);

$sql = "INSERT INTO `".$wpdb->prefix."cform_settings` (`id`, `name`, `text`, `value`, `type`) VALUES
(1, 'manager_email', 'Manager email', 'Manager email', 'text'),
(2, 'button_text', 'Button text', 'Send', 'text'),
(3, 'success_message', 'Success message', 'Thank you for message!', 'text'),
(4, 'save_messages', 'Save messages', '1', 'radio'),
(5, 'message_txt', 'Message', 'Message', 'text');";
dbDelta($sql);

$sql = "INSERT INTO `".$wpdb->prefix."cform_fields` (`id`, `name`, `text`, `placeholder`, `show`, `required`, `type`) VALUES
(1, 'first_name', 'First name', 'First name', 1, 1, 'text'),
(2, 'last_name', 'Last name', 'Last name', 0, 0, 'text'),
(3, 'email', 'Email', 'Email', 1, 1, 'email'),
(4, 'phone', 'Phone', 'Phone', 0, 0, 'text'),
(5, 'address', 'Address', 'Address', 0, 0, 'text');";
dbDelta($sql);
}

/*
 * Deactivate plugin
 */
function pcf_deactivate()
{
	return true;
}


/*
 * Remove plugin
 */
function pcf_uninstall()
{
	global $wpdb;

	$wpdb->query('DROP TABLE IF EXISTS '.$wpdb->prefix.'cform_fields');
	$wpdb->query('DROP TABLE IF EXISTS '.$wpdb->prefix.'cform_message');
	$wpdb->query('DROP TABLE IF EXISTS '.$wpdb->prefix.'cform_settings');
}
