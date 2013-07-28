<?php
/*
Plugin Name: jContactable
Description: This plugin adds a jQuery contactable button to wordpress template.
Author: Jacer Omri
Version: 1.0
Author URI: http://jacer.info
*/

$jc_settings = array(
	  'name' => 'Name Label'
	, 'email' => 'Email Label'
	, 'dropdownTitle' => 'Dropdown Title'
	, 'dropdownOptions' => 'Dropdown Options'
	, 'message' => 'Message Label'
	, 'jc_submit' => 'Submit Label'
	, 'recievedMsg' => 'Received Message'
	, 'notRecievedMsg' => 'Not Received Message'
	, 'disclaimer' => 'disclaimer'
	, 'hideOnSubmit' => 'Hide on Submit'
);

// create custom plugin settings menu
add_action('admin_menu', 'jc_create_menu');

function jc_create_menu() {
	add_submenu_page('options-general.php', 'jContactable Settings', 'jContactable', 'administrator', __FILE__, 'jcontactable_settings_page'/*,plugins_url('/images/icon.png', __FILE__)*/);
	add_action( 'admin_init', 'register_jcontactable' );
}

function register_jcontactable() {
	//register our settings
	global $jc_settings;
	add_settings_section('jcontactable-labels', 'Labels Options', 'jcontactable_labels_code', __FILE__);
	add_settings_section('jcontactable-settings', 'Labels Options', 'jcontactable_settings_code', __FILE__);
	foreach($jc_settings as $setting => $name){
		register_setting( 'jcontactable-settings-group', $setting );
		if($setting == 'hideOnSubmit')
			add_settings_field($setting, $name, 'jcontactable_checkbox_code',  __FILE__, 'jcontactable-settings', array($setting));
		else
			add_settings_field($setting, $name, 'jcontactable_text_code',  __FILE__, 'jcontactable-labels', array($setting));
	}
}
function jcontactable_labels_code() {
	echo '<p>' . _e("This section allow you to configure jContactable Labels") . '</p>';
}

function jcontactable_settings_code() {
	echo '<p>' . _e("This section allow you to configure jContactable Options") . '</p>';
}

function jcontactable_text_code(array $args) {
	echo '<input id="'.$args[0].'" name="'.$args[0].'" type="text" value="'.get_option($args[0]).'" size="50" /><br />';
}

function jcontactable_checkbox_code(array $args) {
	echo '<input id="'.$args[0].'" name="'.$args[0].'" type="checkbox"  ' . checked( get_option($args[0]), 1, false ) . ' value="1" /><br />';
}

register_activation_hook( __FILE__, 'set_up_options' );

function set_up_options(){
	$jc_settings_def = array(
		  'name' => 'Name'
		, 'email' => 'Email'
		, 'dropdownTitle' => 'Issue'
		, 'dropdownOptions' => 	'General, Website bug, Feature request'
		, 'message' => 'Message'
		, 'jc_submit' => 'Send'
		, 'recievedMsg' => 'Thank you for your message'
		, 'notRecievedMsg' => 'Sorry but your message could not be sent, try again later'
		, 'disclaimer' => 'Please feel free to get in touch, we value your feedback'
		, 'hideOnSubmit' => '1'
	);
	foreach($jc_settings_def as $setting => $value)
		add_option($setting, $value);
}

function jcontactable_settings_page() { ?>
	<div class="wrap">
		<h2>jContactable</h2>

		<form method="post" action="options.php">
			<?php settings_fields( 'jcontactable-settings-group' );
				  do_settings_sections(__FILE__);
				  submit_button(); ?>
		</form>
	</div><?php 
}

if(!( is_admin() && ( !defined( 'DOING_AJAX' ) || !DOING_AJAX ) )){
wp_enqueue_script( 'jcontactable.js', plugins_url( 'jquery.contactable.min.js' , __FILE__ ), array('jquery'));
wp_enqueue_script( 'contactable.js', plugins_url( 'contactable.js' , __FILE__ ), array('jquery', 'jcontactable.js'));
wp_enqueue_style( 'contactable.css', plugins_url( 'contactable.css' , __FILE__ ), array()); ?>
	<script type="text/javascript">
	//<![CDATA[
	var jc = {
		subject: 'feedback URL:'+location.href,
		url: '<?php echo plugins_url( 'mail.php' , __FILE__ ); ?>',
		name: '<?php echo addslashes(get_option('name')); ?>',
		email: '<?php echo addslashes(get_option('email')); ?>',
		dropdownTitle: '<?php echo addslashes(get_option('dropdownTitle')); ?>',
		dropdownOptions: ['<?php echo implode("', '", explode(',', get_option('dropdownOptions'))) ?>'],
		message : '<?php echo addslashes(get_option('message')); ?>',
		submit : '<?php echo addslashes(get_option('jc_submit')); ?>',
		recievedMsg : '<?php echo addslashes(get_option('recievedMsg')); ?>',
		notRecievedMsg : '<?php echo addslashes(get_option('notRecievedMsg')); ?>',
		disclaimer: '<?php echo addslashes(get_option('disclaimer')); ?>',
		hideOnSubmit: <?php echo get_option('hideOnSubmit') ? 'true' : 'false'; ?>
	}
	//]]>
	</script>
	<!--start jcontactable -->
	<div id="jcontactable"><!-- jcontactable html placeholder --></div>
	<!--end jcontactable --><?php
}