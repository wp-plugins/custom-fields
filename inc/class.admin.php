<?php
class CF_Admin {

	public $post_type_nav = array();
	public $taxo_nav = array();
	public $taxo = array();
	/**
	 * Constructor
	 * 
	 */
	function CF_Admin() {
		add_action( 'init', array(&$this, 'initPostTypeFields') );
		add_action( 'admin_menu', array(&$this, 'addMenuPage') );
	}
	
	/**
	 * Add Page in menu to all post type
	 *	
	 */
	function initPostTypeFields() {
		global $custom_fields;
		$params = array( 'capability_type' => 'post', 'show_ui' => true ); // To get all post_type depends post
		$post_types = get_post_types($params, 'objects');
		
		$menu_id = array();
		foreach( $post_types as $pt ){
			if($pt->name == 'post')
				$menu_id[$pt->name] = 'edit.php';
			else
				$menu_id[$pt->name] = 'edit.php?post_type=' . $pt->name;
		}
		$menu_id['page'] = 'edit.php?post_type=page';
		
		
		foreach( $menu_id as $post_type => $id_menu ){
			if( isset( $custom_fields['admin-base']->post_type_nav[$post_type] ) )
				continue;
				
			$options_pt = get_option('cf_options-'.$post_type);
			wp_cache_set('cf_options-'.$post_type, $options_pt, FLAG_CACHE, 3600);
			$this->post_type_nav[$post_type] = new SimpleCustomTypes_Admin_PostType( array('name' => $post_type, 'id_menu' => $id_menu), $options_pt );
		}
		
		//Add taxonomy fields
		$params = array( 'show_ui' => true );
		$taxonomies = get_taxonomies($params, 'objects');
		
		$menu_id = array();
		$this->taxo = array();
		foreach( $taxonomies as $taxo_name => $taxo ){
			$this->taxo[$taxo->labels->name] = $taxo_name;
			$options_pt = get_option('cf_options-'.$taxo_name);
			wp_cache_set('cf_options-'.$taxo_name, $options_pt, FLAG_CACHE, 3600);
			$this->taxo_nav[$taxo_name] = new SimpleCustomTypes_Admin_Taxonomy( array('name' => $taxo_name, 'taxo' => $taxo), $options_pt );
		}	
		
	}
	
	function addMenuPage(){
		add_menu_page( 'Taxo Fields', 'Taxo Fields', 'manage_options', 'cf_taxonomies', array(&$this, 'params'), 10 );
	}
	
	function params(){
		?>
		<span>Nothing here now</span>
		<?php
	}
}
?>