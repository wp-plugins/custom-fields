<?php
class CF_Field_Sidebar{
	private $pt;
	
	function __construct( $obj_pt ){
		$this->pt = &$obj_pt;
	}
	
	function cf_get_sidebars_fields($deprecated = true) {
		if ( $deprecated !== true )
			_deprecated_argument( __FUNCTION__, '2.8.1' );
		//global $this->pt->_cf_sidebars_fields;
		// If loading from front page, consult $this->pt->_cf_sidebars_fields rather than options
		// to see if cf_convert_field_settings() has made manipulations in memory.
		if ( !is_admin() ) {
			if ( empty($this->pt->_cf_sidebars_fields) )
				$this->pt->_cf_sidebars_fields = $this->pt->sidebars_fields;
	
			$sidebars_fields = $this->pt->_cf_sidebars_fields;
		} else {
			$sidebars_fields = $this->pt->sidebars_fields;
			$_sidebars_fields = array();
			if ( isset($sidebars_fields['cf_inactive_fields']) || empty($sidebars_fields) )
				$sidebars_fields['array_version'] = 3;
			elseif ( !isset($sidebars_fields['array_version']) )
				$sidebars_fields['array_version'] = 1;
				
			switch ( $sidebars_fields['array_version'] ) {
				case 1 :
					foreach ( (array) $sidebars_fields as $index => $sidebar )
					if ( is_array($sidebar) )
					foreach ( (array) $sidebar as $i => $name ) {
						$id = strtolower($name);
						if ( isset($this->pt->cf_registered_fields[$id]) ) {
							$_sidebars_fields[$index][$i] = $id;
							continue;
						}
						$id = sanitize_title($name);
						if ( isset($this->pt->cf_registered_fields[$id]) ) {
							$_sidebars_fields[$index][$i] = $id;
							continue;
						}
	
						$found = false;
	
						foreach ( $this->pt->cf_registered_fields as $field_id => $field ) {
							if ( strtolower($field['name']) == strtolower($name) ) {
								$_sidebars_fields[$index][$i] = $field['id'];
								$found = true;
								break;
							} elseif ( sanitize_title($field['name']) == sanitize_title($name) ) {
								$_sidebars_fields[$index][$i] = $field['id'];
								$found = true;
								break;
							}
						}
	
						if ( $found )
							continue;
	
						unset($_sidebars_fields[$index][$i]);
					}
					$_sidebars_fields['array_version'] = 2;
					$sidebars_fields = $_sidebars_fields;
					unset($_sidebars_fields);
	
				case 2 :
					$sidebars = array_keys($this->pt->cf_registered_fields );
					if ( !empty( $sidebars ) ) {
						// Move the known-good ones first
						foreach ( (array) $sidebars as $id ) {
							if ( array_key_exists( $id, $sidebars_fields ) ) {
								$_sidebars_fields[$id] = $sidebars_fields[$id];
								unset($sidebars_fields[$id], $sidebars[$id]);
							}
						}
	
						// move the rest to wp_inactive_fields
						if ( !isset($_sidebars_fields['cf_inactive_fields']) )
							$_sidebars_fields['cf_inactive_fields'] = array();
	
						if ( !empty($sidebars_fields) ) {
							foreach ( $sidebars_fields as $lost => $val ) {
								if ( is_array($val) )
									$_sidebars_fields['cf_inactive_fields'] = array_merge( (array) $_sidebars_fields['cf_inactive_fields'], $val );
							}
						}
	
						$sidebars_fields = $_sidebars_fields;
						unset($_sidebars_fields);
					}
			}
		}
	
		if ( is_array( $sidebars_fields ) && isset($sidebars_fields['array_version']) )
			unset($sidebars_fields['array_version']);
		
		$this->pt->sidebars_fields = apply_filters('sidebars_fields', $sidebars_fields);

		return $sidebars_fields;
	}
	
	function cf_set_sidebars_fields( $sidebars_fields ) {
		if ( !isset( $sidebars_fields['array_version'] ) )
			$sidebars_fields['array_version'] = 3;
		$this->pt->sidebars_fields = $sidebars_fields;
		$this->pt->update_var('sidebars_fields');
	}
	
	function cf_register_sidebar_field($id, $name, $output_callback, $save_callback, $options = array()) {
		$id = strtolower($id);
		if ( empty($output_callback) ) {
			unset($this->pt->cf_registered_fields[$id]);
			return;
		}

		$id_base = $this->pt->cf_field_manager->_get_field_id_base($id);
		if ( in_array($output_callback, $this->pt->_cf_deprecated_fields_callbacks, true) && !is_callable($output_callback) ) {
			if ( isset($this->pt->cf_registered_field_controls[$id]) )
				unset($this->pt->cf_registered_field_controls[$id]);

			if ( isset($this->pt->cf_registered_field_updates[$id_base]) )
				unset($this->pt->cf_registered_field_updates[$id_base]);
	
			return;
		}
		
		$defaults = array('classname' => $output_callback);
		$options = wp_parse_args($options, $defaults);
		$field = array(
			'name' => $name,
			'id' => $id,
			'callback' => $output_callback,
			'save_callback' => $save_callback,
			'params' => array_slice(func_get_args(), 5)
		);
		$field = array_merge($field, $options);

		if ( is_callable($output_callback) && ( !isset($this->pt->cf_registered_fields[$id]) || did_action( 'fields_init' ) ) ) {
			do_action( 'cf_register_sidebar_field', $field );
			$this->pt->cf_registered_fields[$id] = $field;
			$this->pt->update_var('cf_registered_fields');
		}
	}
}