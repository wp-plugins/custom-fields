<?php
class CF_Page_Field{
	
	private $pt;
	
	function __construct( $obj_pt ){
		$this->pt = &$obj_pt;
		add_action( 'admin_menu', array(&$this, 'submenu' ) );
	}
	
	function submenu(){
		add_submenu_page( $this->pt->id_menu, __('Fields', 'custom_fields'), __('Fields', 'custom_fields'), 'manage_options', 'custom_fields-' . $this->pt->post_type, array(&$this, 'displayAdminFormFields') );
	}
	
	function displayAdminFormFields(){
		global $current_screen;
		$this->pt->get_var('sidebars_fields');
		include( SCF_DIR . '/inc/admin.tpl.php' );
	}
	
	function retrieve_fields() {
		//global $cf_registered_field_updates;
		$_sidebars_fields = array();
		$sidebars = array_keys($this->pt->cf_registered_sidebars);
		unset( $this->pt->sidebars_fields['array_version'] );
		$old = array_keys($this->pt->sidebars_fields);
		sort($old);
		sort($sidebars);

		//if ( $old == $sidebars || count($sidebars) > count($old))
		//	return;
	
		// Move the known-good ones first
		foreach ( $sidebars as $id ) {
			if ( array_key_exists( $id, $this->pt->sidebars_fields ) ) {
				$_sidebars_fields[$id] = $this->pt->sidebars_fields[$id];
				unset($this->pt->sidebars_fields[$id], $sidebars[$id]);
			}
		}
	
		// if new theme has less sidebars than the old theme
		if ( !empty($this->pt->sidebars_fields) ) {
			foreach ( $this->pt->sidebars_fields as $lost => $val ) {
				if ( is_array($val) && isset($_sidebars_fields['cf_inactive_fields']) )
					$_sidebars_fields['cf_inactive_fields'] = array_merge( (array) $_sidebars_fields['cf_inactive_fields'], $val );
				elseif ( is_array($val) )
					$_sidebars_fields['cf_inactive_fields'] = $val;
			}
		}
		
		// discard invalid, theme-specific fields from sidebars
		$shown_fields = array();
		foreach ( $_sidebars_fields as $sidebar => $fields ) {
			if ( !is_array($fields) )
				continue;
	
			$_fields = array();
			foreach ( $fields as $field ) {
				if ( isset($this->pt->cf_registered_fields[$field]) )
					$_fields[] = $field;
			}
			$_sidebars_fields[$sidebar] = $_fields;
			$shown_fields = array_merge($shown_fields, $_fields);
		}

		$this->pt->sidebars_fields = $_sidebars_fields;
		unset($_sidebars_fields, $_fields);
		
		// find hidden/lost multi-field instances
		$lost_fields = array();
		foreach ( $this->pt->cf_registered_fields as $key => $val ) {
			if ( in_array($key, $shown_fields, true) )
				continue;
	
			$number = preg_replace('/.+?-([0-9]+)$/', '$1', $key);
	
			if ( 2 > (int) $number )
				continue;
	
			//$lost_fields[] = $key;
		}
		$this->pt->sidebars_fields['cf_inactive_fields'] = array_merge($lost_fields, (array) $this->pt->sidebars_fields['cf_inactive_fields']);
				
		$this->pt->cf_field_sidebar->cf_set_sidebars_fields($this->pt->sidebars_fields);
	}
}