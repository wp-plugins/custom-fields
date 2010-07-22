<?php
class CF_Sidebar_Field{

	private $pt;
	
	function __construct( $obj_pt ){
		$this->pt = &$obj_pt;
		
		//add_action( 'cf_register_sidebar', 		array(&$this, 'update_registered_sidebar') );
		//add_action( 'cf_unregister_sidebar', 	array(&$this, 'update_registered_sidebar') );
	}
	
	function cf_register_sidebars($number = 1, $args = array()) {
		$number = (int) $number;
	
		if ( is_string($args) )
			parse_str($args, $args);
	
		for ( $i = 1; $i <= $number; $i++ ) {
			$_args = $args;
	
			if ( $number > 1 )
				$_args['name'] = isset($args['name']) ? sprintf($args['name'], $i) : sprintf(__('Sidebar %d'), $i);
			else
				$_args['name'] = isset($args['name']) ? $args['name'] : __('Sidebar');
	
			// Custom specified ID's are suffixed if they exist already.
			// Automatically generated sidebar names need to be suffixed regardless starting at -0
			if ( isset($args['id']) ) {
				$_args['id'] = $args['id'];
				$n = 2; // Start at -2 for conflicting custom ID's
				while ( isset($this->pt->cf_registered_sidebars[$_args['id']]) )
					$_args['id'] = $args['id'] . '-' . $n++;
			} else {
				$n = count($this->pt->cf_registered_sidebars);
				do {
					$_args['id'] = 'sidebar-' . ++$n;
				} while ( isset($this->pt->cf_registered_sidebars[$_args['id']]) );
			}
			$this->pt->cf_register_sidebar($_args);
		}
	}
	
	function cf_register_sidebar($args = array()) {
		$i = count($this->pt->cf_registered_sidebars) + 1;
		$defaults = array(
			'name' => sprintf(__('Sidebar %d'), $i ),
			'id' => "sidebar-$i",
			'description' => '',
			'before_field' => '<li id="%1$s" class="field %2$s">',
			'after_field' => "</li>\n",
			'before_title' => '<h2 class="fieldtitle">',
			'after_title' => "</h2>\n",
		);
	
		$sidebar = wp_parse_args( $args, $defaults );
	
		$this->pt->cf_registered_sidebars[$sidebar['id']] = $sidebar;
		$this->pt->update_var('cf_registered_sidebars');
		add_theme_support('fields');
	
		do_action( 'cf_register_sidebar', $sidebar );
	
		return $sidebar['id'];
	}
	
	function cf_unregister_sidebar( $name ) {
		if ( isset( $this->pt->cf_registered_sidebars[$name] ) ){
			unset( $this->pt->cf_registered_sidebars[$name] );
			$this->pt->update_var('cf_registered_sidebars');
			//do_action( 'cf_unregister_sidebar', $sidebar );
		}
	}
	/*
	function update_registered_sidebar(){

		$options['cf_registered_sidebars'] 	= $this->pt->cf_registered_sidebars;
		$options['sidebars_fields'] 		= $this->pt->sidebars_fields;
		update_option( 'cf_options-' . $this->pt->post_type, $options );

	}
	*/
	function dynamic_sidebar($index = 1) {
		if ( is_int($index) ) {
			$index = "sidebar-$index";
		} else {
			$index = sanitize_title($index);
			foreach ( (array) $this->pt->cf_registered_sidebars as $key => $value ) {
				if ( sanitize_title($value['name']) == $index ) {
					$index = $key;
					break;
				}
			}
		}
		
		$sidebars_fields = $this->pt->cf_field_sidebar->cf_get_sidebars_fields();

		if ( empty($this->pt->cf_registered_sidebars[$index]) || !array_key_exists($index, $sidebars_fields) || !is_array($sidebars_fields[$index]) || empty($sidebars_fields[$index]) )
			return false;
		
		$sidebar = $this->pt->cf_registered_sidebars[$index];

		$did_one = false;
		
		foreach ( (array) $sidebars_fields[$index] as $id ) {
			if ( !isset($this->pt->cf_registered_fields[$id]) ) continue;

			$params = array_merge(
				array( array_merge( $sidebar, array('field_id' => $id, 'field_name' => $this->pt->cf_registered_fields[$id]['name']) ) ),
				(array) $this->pt->cf_registered_fields[$id]['params']
			);
			// Substitute HTML id and class attributes into before_widget
			$classname_ = '';
			foreach ( (array) $this->pt->cf_registered_fields[$id]['classname'] as $cn ) {
				if ( is_string($cn) )
					$classname_ .= '_' . $cn;
				elseif ( is_object($cn) )
					$classname_ .= '_' . get_class($cn);
			}

			$classname_ = ltrim($classname_, '_');
			$params[0]['before_field'] = sprintf($params[0]['before_field'], $id, $classname_);

			$params = apply_filters( 'dynamic_sidebar_params', $params );
			$params[0]['post_type'] = $this->pt->post_type;
			$callback = $this->pt->cf_registered_fields[$id]['callback'];
			do_action( 'dynamic_sidebar', $this->pt->cf_registered_fields[$id] );
			if ( is_callable( array(&$this->pt->cf_field_control, $callback) ) ) {
				call_user_func_array( array(&$this->pt->cf_field_control, $callback), $params);
				$did_one = true;
			}
		}
		return $did_one;
	}
}