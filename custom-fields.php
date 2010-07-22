<?php
/*
Plugin Name: Custom Fields for WordPress
Version: 2.0.0-beta
Plugin URI: http://redmine.beapi.fr/projects/show/simple-taxonomy
Description: This plugin add custom fields for some things on WordPress, blog, term taxonomy and custom object types
Author: Julien Guilmont & Amaury Balmer
Author URI: http://www.beapi.fr

----

Copyright 2010 Julien Guilmont & Amaury Balmer (julien.guilmont@beapi.fr & amaury@beapi.fr)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

---

*/

// Folder name
define ( 'SCF_VERSION', '1.0.0-alpha' );
define ( 'SCF_OPTION',  'custom-fields' );
define ( 'SCF_FOLDER',  'custom-fields' );
define ( 'FLAG_CACHE', 'Fields' );

// mu-plugins or regular plugins ? 
if ( is_dir(WPMU_PLUGIN_DIR . DIRECTORY_SEPARATOR . SCF_FOLDER ) ) {
	define ( 'SCF_DIR', WPMU_PLUGIN_DIR . DIRECTORY_SEPARATOR . SCF_FOLDER );
	define ( 'SCF_URL', WPMU_PLUGIN_URL . '/' . SCF_FOLDER );
} else {
	define ( 'SCF_DIR', WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . SCF_FOLDER );
	define ( 'SCF_URL', WP_PLUGIN_URL . '/' . SCF_FOLDER );
}

// Library
require( SCF_DIR . '/inc/functions.php' );

	// Call admin class
	require( SCF_DIR . '/inc/class.admin.php' );
	require( SCF_DIR . '/inc/class.page.php' );
	require( SCF_DIR . '/inc/class.ajax.php' );
	require( SCF_DIR . '/inc/class.sidebar.php' );
	require( SCF_DIR . '/inc/class.admin.posttype.php' );
	//require( SCF_DIR . '/inc/class.admin.widget.php');
	require( SCF_DIR . '/inc/class.field.base.php');
	require( SCF_DIR . '/inc/class.field.factory.php');	
	require( SCF_DIR . '/inc/class.field.manager.php');	
	require( SCF_DIR . '/inc/class.field.sidebar.php');
	require( SCF_DIR . '/inc/class.field.control.php');
	require( SCF_DIR . '/inc/class.admin.object.php');
	require( SCF_DIR . '/inc/class.admin.object.taxo.php');
	require( SCF_DIR . '/inc/class.admin.taxo.php');
	require( SCF_DIR . '/inc/class.page.taxo.php' );
	
	// Call built'in composants
	require( SCF_DIR . DIRECTORY_SEPARATOR . 'composants' . DIRECTORY_SEPARATOR . 'default-fields.php' );
	require( SCF_DIR . DIRECTORY_SEPARATOR . 'composants' . DIRECTORY_SEPARATOR . 'date-picker'    . DIRECTORY_SEPARATOR . 'date-picker.php' );
	require( SCF_DIR . DIRECTORY_SEPARATOR . 'composants' . DIRECTORY_SEPARATOR . 'dropdown-users' . DIRECTORY_SEPARATOR . 'dropdown-users.php' );


add_action( 'plugins_loaded', 'initCustomFields' );
function initCustomFields() {
	global $custom_fields;
	
	// Load translations
	load_plugin_textdomain ( 'custom-fields', false, SCF_FOLDER . 'languages' );

	$custom_fields['admin-base'] 	= new CF_Admin();	

}
?>