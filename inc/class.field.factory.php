<?php
/**
 * Register all of the default WordPress widgets on startup.
 *
 * Calls 'widgets_init' action after all of the WordPress widgets have been
 * registered.
 *
 * @since 2.2.0
 */
/*

/**
 * Singleton that registers and instantiates WP_field classes.
 *
 * @package WordPress
 * @subpackage fields
 * @since 2.8
 */
class CF_Field_Factory {
	public $fields;
	private $pt;
	
	function __construct( $obj_pt ) {
		$this->pt = &$obj_pt;
		//var_dump($pt->post_type);
		add_action( 'fields_init-' . $this->pt->post_type, array( &$this, '_register_fields' ), 100 );
	}

	function register($field_class) {
		$this->fields[$field_class] = & new $field_class();
	}

	function unregister($field_class) {
		if ( isset($this->fields[$field_class]) )
			unset($this->fields[$field_class]);
	}

	function _register_fields( $obj ) {
		//var_dump($this->pt->post_type);
		//var_dump($obj->post_type);
		//remove_action( 'fields_init-' . $obj->post_type, array( &$this, '_register_fields' ), 100 );
		
		$keys = array_keys($this->fields);
		if(is_array($obj->cf_registered_fields)){
			$registered = array_keys($obj->cf_registered_fields);
		}else{
			$registered = array();
		}
		
		$registered = array_map( array(&$obj->cf_field_manager, '_get_field_id_base'), $registered);
		//$registered = array();
		foreach ( $keys as $key ) {
			
			if ( in_array($this->fields[$key]->id_base, $registered, true)) {
				unset($this->fields[$key]);
				continue;
			}
			
			$this->fields[$key]->_register(&$obj);
		}
		
		
	}
}

?>