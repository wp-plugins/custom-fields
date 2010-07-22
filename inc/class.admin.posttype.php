<?php
class SimpleCustomTypes_Admin_PostType{
	
	public $post_type;
	public $id_menu;
	
	public $sidebars_fields;
	public $cf_registered_sidebars;
	public $cf_registered_fields;
	public $cf_registered_field_controls;
	
	public $cf_field_factory;
	public $cf_ajax;
	public $cf_page;
	public $cf_sidebar;
	public $cf_field_manager;
	public $cf_field_sidebar;
	public $option_fields;
	public $_cf_sidebars_fields;
	public $sidebars;
	
	public $_cf_deprecated_fields_callbacks = array(
		'widget_input',
		'wp_widget_pages_control',
		'wp_widget_calendar',
		'wp_widget_calendar_control',
		'wp_widget_archives',
		'wp_widget_archives_control',
		'wp_widget_links',
		'wp_widget_meta',
		'wp_widget_meta_control',
		'wp_widget_search',
		'wp_widget_recent_entries',
		'wp_widget_recent_entries_control',
		'wp_widget_tag_cloud',
		'wp_widget_tag_cloud_control',
		'wp_widget_categories',
		'wp_widget_categories_control',
		'wp_widget_text',
		'wp_widget_text_control',
		'wp_widget_rss',
		'wp_widget_rss_control',
		'wp_widget_recent_comments',
		'wp_widget_recent_comments_control'
	);
	
	function SimpleCustomTypes_Admin_PostType( $post_type, $options ) {
		$this->__construct( $post_type, $options );
	}
	
	function __construct( $post_type, $options ){
		$this->post_type 					= $post_type['name'];
		$this->id_menu 						= $post_type['id_menu'];
		//$options = array();
		//wp_cache_set('cf_options-'.$this->post_type, $options, FLAG_CACHE, 3600);
		$options = wp_cache_get('cf_options-'.$this->post_type, FLAG_CACHE);
		
		if( !empty($options) ) {
			$this->cf_registered_sidebars 		= isset($options['cf_registered_sidebars']) ? (array) $options['cf_registered_sidebars'] : array();
			$this->sidebars_fields 				= isset($options['sidebars_fields']) ? (array) $options['sidebars_fields'] : array('cf_inactive_fields' => array(), 'array_version' => 3);
			$this->cf_registered_fields			= isset($options['cf_registered_fields']) ? (array) $options['cf_registered_fields'] : array();
			$this->option_fields				= isset($options['option_fields']) ? (array) $options['option_fields'] : array();
			$this->sidebars						= isset($options['sidebars']) ? (array) $options['sidebars'] : array();
			$this->cf_registered_field_updates 	= isset($options['cf_registered_field_updates']) ? (array) $options['cf_registered_field_updates'] : array();
			$this->cf_registered_field_controls	= isset($options['cf_registered_field_controls']) ? (array) $options['cf_registered_field_controls'] : array();
			$this->update_var('sidebars_fields');
			
		} else {
			$this->sidebars_fields = array('cf_inactive_fields' => array(), 'array_version' => 3);
			$this->update_var('sidebars_fields');
		}
		//var_dump($this->cf_registered_field_controls);
		$this->cf_field_factory =& new CF_Field_Factory	(&$this);
		$this->cf_ajax 			=& new CF_Ajax_Field	(&$this);
		$this->cf_page 			=& new CF_Page_Field	(&$this);
		$this->cf_sidebar 		=& new CF_Sidebar_Field	(&$this);
		$this->cf_field_manager	=& new CF_Field_Manager	(&$this);
		$this->cf_field_sidebar	=& new CF_Field_Sidebar	(&$this);
		$this->cf_field_control	=& new CF_Field_Controle(&$this);
		$this->cf_admin_object	=& new CF_Admin_Object(&$this);
		
		add_action( 'cf_init-'.$this->post_type, array(&$this, 'cf_fields_init') );
		
		add_action( 'wp_loaded', array(&$this, 'cf_fields_load') , 1);
	}
	
	function cf_fields_load(){
		do_action( 'cf_init-'.$this->post_type );
	}
	
	function cf_fields_init() {

		if ( !is_blog_installed() )
			return;
		
		$this->cf_field_manager->register_field('CF_Field_Input');
		$this->cf_field_manager->register_field('CF_Field_Textarea');
		$this->cf_field_manager->register_field('CF_Field_EditorLight');
		$this->cf_field_manager->register_field('CF_Field_Editor');
		$this->cf_field_manager->register_field('CF_Field_Select');
		$this->cf_field_manager->register_field('CF_Field_SelectMultiple');
		$this->cf_field_manager->register_field('CF_Field_Checkbox');
		$this->cf_field_manager->register_field('CF_Field_DatePicker');
		$this->cf_field_manager->register_field('CF_Field_Dropdown_Users');
		$this->cf_field_manager->register_field('CF_Field_Media');
		do_action('fields_init-' . $this->post_type, &$this);
		
		$this->get_var('sidebars');
		if( isset($this->sidebars) && is_array($this->sidebars) ){
			foreach( $this->sidebars as $sidebar ){
				$this->cf_sidebar->cf_register_sidebar( $sidebar );
			}
		}
		$field_ar = array();
		foreach($this->cf_registered_fields as $field => $value){

			foreach($this->sidebars_fields as $name => $sidebar){
				if( (!in_array($field, (array)$sidebar) && !strripos($field, '-2') && !isset($_POST['field-id'])) || ( isset($_POST['field-id']) && $_POST['field-id'] != $field && !in_array($field, (array)$sidebar) && !strripos($field, '-2')) ){
				}else{
					if( in_array($field, $field_ar) )
						continue;
					$field_ar[$field] = $value;
				}
			}

		}

		foreach( array_diff_key( $this->cf_registered_fields, $field_ar ) as $field => $value){
			unset($this->cf_registered_fields[$field]);
		}
		$this->update_var('cf_registered_fields');
	}
	//Must add cache manager
	function update_var( $field = null ){
		$options = array();
		$flag = true;

		if($field == null){
			$options['cf_registered_sidebars'] 		= $this->cf_registered_sidebars;
			$options['sidebars_fields'] 			= $this->sidebars_fields;
			$options['cf_registered_fields']		= $this->cf_registered_fields;
			//$options['option_fields']				= $this->option_fields;
		}else{
			$options = wp_cache_get('cf_options-'.$this->post_type, FLAG_CACHE);
			//var_dump($options['sidebars']);
			
			
			//$options = get_option('cf_options-'.$this->post_type);
			//$this->p_options = $options;
			
			if( !isset($options[$field]) || $options[$field] != $this->$field ) {
					
				$options[$field] = $this->$field;
			} else {
				$flag = false;
			}
			
		}
		//$options = array();
		
		if($flag == true){
			update_option( 'cf_options-'.$this->post_type, $options );
			wp_cache_replace('cf_options-'.$this->post_type, $options, FLAG_CACHE, 3600);
		}
	}
	
	function get_var( $field = null ){
			$options = wp_cache_get('cf_options-'.$this->post_type, FLAG_CACHE);
		if( $field == null ){
			$this->cf_registered_sidebars 	= (array)$options['cf_registered_sidebars'];
			$this->sidebars_fields			= (array)$options['sidebars_fields'];
			$this->cf_registered_fields 	= (array)$options['cf_registered_fields'];
			$this->option_fields 			= (array)$options['option_fields'];
		}else{
			if( isset($options[$field]) )
				$this->$field = (array)$options[$field];
			else
				$this->$field = array();
		}
	}

}