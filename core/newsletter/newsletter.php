<?php
function basetheme_newsletter_init()
{
	add_action('wp_ajax_saveBasethemeNewsletter', 'saveBasethemeNewsletter');
	add_action('wp_ajax_nopriv_saveBasethemeNewsletter', 'saveBasethemeNewsletter');
	// basetheme newsletter plugin

	function saveBasethemeNewsletter(){
		global $wpdb;
		$datesent = date('Y-m-d H:i:s');  
		$email = sanitize_text_field($_POST['email']);
		$REMOTE_ADDR = $_SERVER['REMOTE_ADDR'];
		if(empty(trim($email)) || !is_email($email)) {
			echo Config::$newsletter_options['basetheme_newsletter_wrong_email'];
			die();
		}
		
	    $table_name = $wpdb->prefix . 'basetheme_newsletter';
	    $cEMAIL = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE email = '".$email."'");

	    if ($cEMAIL < 1)
	    {
			$wpdb->insert($table_name, ['email' => $email, 'date' => $datesent, 'remote_addr' => $REMOTE_ADDR], ['%s','%s','%s','%s']);
			
			// PREPARE THE BODY OF THE MESSAGE
			$message = '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/1999/REC-html401-19991224/strict.dtd">
			<html>
				<head>
					<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
					<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
					<title>تم تسجيلك في النشرة البريدية بنجاح</title>
					<style>
					@import "http://fonts.googleapis.com/earlyaccess/droidarabickufi.css";
					html {
					    font-family: "Droid Arabic Kufi",serif !important;
					    height: 100%;
					    overflow: hidden;
					}
					table {
					    border-collapse: collapse;
					}

					td {
					    padding-top: 3em;
					    padding-bottom: 3em;
					}
					</style>
				</head>
				<body style="-webkit-text-size-adjust:none;background-color:#fe4365;font-family:Droid Arabic Kufi;color:#fff;line-height:18px;margin:auto;border-collapse: collapse;border-spacing: 0;width: 600px;line-height: 1.5;">
					'.Config::$newsletter_options['basetheme_newsletter_email_content'].'
				</body>
			</html>';
			//   Email Infos
			$to = $email;
			$subject = Config::$newsletter_options['basetheme_newsletter_email_content'];
			$from = Config::$newsletter_options['basetheme_newsletter_email'];
			$headers = "From: " . $from . "\r\n";
			$headers .= "Reply-To: ". strip_tags($_POST['email']) . "\r\n";
			$headers .= "MIME-Version: 1.0\r\n";
			$headers .= "Content-Type: text/html; charset=UTF-8\r\n";
			// Send Email
			
			mail($to, $subject, $message, $headers);
			echo Config::$newsletter_options['basetheme_newsletter_registration_success'];
		}else {
			echo Config::$newsletter_options['basetheme_newsletter_registration_error'];
		}
		wp_die();
	}
}
add_action( 'init', 'basetheme_newsletter_init' );


add_shortcode('basetheme_newsletter', function(){
	if (!Config::$newsletter_options['basetheme_show_newsletter']) return;
	return '<div class="newsletter">
		<div class="block-title"><span>
			'.Config::$newsletter_options['basetheme_newsletter_description'].'
		</span></div>
		<div class="newsletterForm input-group">
			<input type="email" id="email" placeholder="البريد الإلكتروني" class="newsletter form-control" name="email"/>
			<span class="input-group-btn">
				<button class="btn btn-blue newsletterBtn" type="submit"><i class="fa fa-send nopadding"></i></button>
			</span>
			<div class="statusNewsletter"></div>
		</div>
	</div>'; 
});

function basetheme_newsletter_db() {
	require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );
	global $wpdb;
	$db_table_name = $wpdb->prefix . 'basetheme_newsletter';
	if( $wpdb->get_var( "SHOW TABLES LIKE '$db_table_name'" ) != $db_table_name ) {
		if ( ! empty( $wpdb->charset ) )
			$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
		if ( ! empty( $wpdb->collate ) )
			$charset_collate .= " COLLATE $wpdb->collate";
 
		$sql = "CREATE TABLE " . $db_table_name . " (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`name` varchar(250) DEFAULT NULL,
			`email` varchar(250) DEFAULT NULL,
			`date` varchar(250) DEFAULT NULL,
			`remote_addr` varchar(250) DEFAULT NULL,
			PRIMARY KEY (`id`)
		) $charset_collate;";
		dbDelta( $sql );
	}
}
add_action( 'wp_loaded', 'basetheme_newsletter_db' );
?>