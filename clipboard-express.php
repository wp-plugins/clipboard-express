<?php
/*
Plugin Name: Clipboard Express
Plugin URI: http://blog.ninedays.org/2008/05/07/clipboard-express-wordpress-plugin/
Description: Need a clipboard to store stuff?  Well here ya go.  Each user has their own private clipboard to store content, information and notes. <a href="edit.php?page=clipboard">Clipboard Express</a>
Version: 1.2
License: GPL
Author: Terri Ann Swallow
Author URI: http://www.ninedays.org/

// Copyright (c) 2008 Terri Ann Swallow. All rights reserved.
//
// Released under the GPL license
// http://www.opensource.org/licenses/gpl-license.php
//
// This is an add-on for WordPress
// http://wordpress.org/
//
// **********************************************************************
// This script is distributed in the hope that it will be useful, but
// WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
// **********************************************************************
*/



function set_clipboard_options(){
	add_option('clipboard_data', '', 'Clipboard Data');
}

function unset_clipboard_image_options(){
}

register_activation_hook(__FILE__,'set_clipboard_options');
register_deactivation_hook(__FILE__,'unset_clipboard_image_options');


//********************************************//
// Actions
//********************************************//

// Notify box in advanced mode
add_action('edit_form_advanced', 'clipboard_post_display');

// Notify box in simple mode
add_action('simple_edit_form', 'clipboard_post_display');

// Notify box in page mode
add_action('edit_page_form', 'clipboard_post_display');

// save clipboard edits in post
add_action('edit_post', 'update_clipboard_post', 5); 


add_action('admin_menu', 'add_clipboard_menu');

function add_clipboard_menu(){
	add_management_page('Clipboard Express','Clipboard Express', 0, 'clipboard', 'clipboard_form_render');
	
    add_submenu_page('post.php', 'Clipboard Express', 'Clipboard Express', 0, 'edit.php?page=clipboard'); // adds to the Write menu
	
}


function clipboard_form_render(){

	echo '<div class="wrap">' . "\n";
	echo '<h2>Clipboard Express</h2>' . "\n";
	if($_REQUEST['submit']){
		update_clipboard_options();
	}
	print_clipboard_form();	
	echo '</div>' . "\n";
}

function update_clipboard_post(){
	global $current_user;
	$ok = true;
	
	if(isset($_REQUEST['modify_clipboard']) && $_REQUEST['modify_clipboard'] == 1){
		$clipboard_data = $_REQUEST['clipboard_data'];
		
		$all_clipped_up = unserialize(get_option('clipboard_data'));
		$all_clipped_up[$current_user->ID] = $clipboard_data;
		
		$clipboard_data_save = serialize($all_clipped_up);
		
		update_option('clipboard_data', $clipboard_data_save);
	}
}

function update_clipboard_options(){
	global $current_user;
	$ok = true;
	
	$clipboard_data = $_REQUEST['clipboard_data'];
	
	$all_clipped_up = unserialize(get_option('clipboard_data'));
	$all_clipped_up[$current_user->ID] = $clipboard_data;
	
	$clipboard_data_save = serialize($all_clipped_up);
	
	update_option('clipboard_data', $clipboard_data_save);
	
	if(!$ok){
		echo '<div id="message" class="error fade">';
		echo '<p>Failed to save clipboard</p>';
		echo '</div>';
	} else {
		echo '<div id="message" class="update fade">';
		echo '<p>Clipboard Saved</p>';
		echo '</div>';
	}
}

function print_clipboard_form() {
	global $current_user;

	$get_clipboard_data = unserialize(get_option('clipboard_data'));
	
	$default_clipboard_data = stripslashes($get_clipboard_data[$current_user->ID]);
	
	if(empty($default_clipboard_data)) $default_clipboard_data = '';
   ?>
		<form method="post">
		<p>Enter your clipboard data here</p>
		<textarea cols="100" rows="25" name="clipboard_data"><?php echo $default_clipboard_data; ?></textarea>
		<p>
		<input type="submit"  name="submit" value="Save" />
		</p>
	<?php
}


function clipboard_post_display() {
	
	global $current_user;

	$get_clipboard_data = unserialize(get_option('clipboard_data'));
	
	$default_clipboard_data = stripslashes($get_clipboard_data[$current_user->ID]);	

	echo '
	<div id="clipboard-express" class="postbox ">
	<h3>Clipboard Express</h3>
	<div class="inside">
	<textarea id="clipboard_data" tabindex="6" name="clipboard_data" cols="40" rows="1" style="height:15em; margin:0pt; width:98%;">'. $default_clipboard_data .'</textarea>
	<p><label for="modify_clipboard"><input type="checkbox" name="modify_clipboard" id="modify_clipboard" value="1" /> Save clipboard changes?</label></p>
	</div>
	</div>
	';
}

?>