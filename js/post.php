<?php
if ( isset($_POST['arr']) ) {
	require('../../../../wp-load.php');
	
	//create settings array from plugin "settings page"
	$settings = $wpdb->get_results('SELECT * FROM `'.$wpdb->prefix.'cform_settings`');
	foreach ($settings as $k => $v) {
		$arr_settings[$v->name] = $v->value;
	}
	
	//create settings array from plugin "contact form"
	$arr_field = $wpdb->get_results('SELECT * FROM `'.$wpdb->prefix.'cform_fields` WHERE `show`="1"');
	foreach ($arr_field as $k => $v){
		$arr_fields['pcf_'.$v->name] = $v->placeholder;
	}

	//create message
	$from = '';
	$message = '';
	foreach ($arr_fields as $k=>$v) {
		if ($from == '') $from = substr($_POST['arr'][$k], 0, 500);
		$message .= '<div><b>'.$v.'</b>: '.addslashes(htmlentities(substr($_POST['arr'][$k], 0, 500))).'</div>';
	}
	$message_txt = addslashes(htmlentities(substr($_POST['arr']['pcf_message'], 0, 500)))."\r\n";

	$headers = "From: " . strip_tags($from) . "\r\n";
	$headers .= "MIME-Version: 1.0\r\n";
	$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
	
	//send mail
	mail($arr_settings['manager_email'], "Contact form", $message.'----<br />'.$message_txt, $headers);
	
	//save to DB
	if ( $arr_settings['save_messages'] == "1" ) {
		$wpdb->query('INSERT INTO `'.$wpdb->prefix.'cform_message` (`data`,`message`,`date`) VALUES ("'.$message.'","'.$message_txt.'","'.time().'")');
	}
}
?>