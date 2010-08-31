<?php
class CF_Admin_Object {
	private $pt;
	/*
	 * Constructor
	 **/
	function __construct( $obj_pt ) {

		$this->pt = &$obj_pt;
		// Check Ajax request for display media insert function
		add_action( 'wp_ajax_insert_custom_field_media', array(&$this, 'checkMediaInsert') );
		add_action( 'wp_ajax_ct_preview_media', array(&$this, 'checkAjaxPreview') );
		
		// Add links on media manager if custom fields need it.
		add_filter( 'media_meta', array(&$this, 'addMediaLinksOnMeta'), 10, 2 );
		
		// Register Javascript need for custom fields
		add_action( 'admin_enqueue_scripts', array(&$this, 'initStyleScript'), 10 );
		
		// Save custom datas
		add_action( 'save_post', array(&$this, 'saveCustomFields'), 10, 2 );

		// Add blocks on write page
		add_action( 'add_meta_boxes', array(&$this, 'initCustomFields'), 10, 1 );
		
	}

	/**
	 * Load JS and CSS need for admin features.
	 * 
	 */
	function initStyleScript( $hook_sufix ) {
		global $post_type;
		if ( $hook_sufix == 'post-new.php' ) {
		
			// Add CSS for boxes
			wp_enqueue_style ( 'simple-custom-types-object', SCF_URL.'/inc/css/admin.css', array(), SCF_VERSION);

			// Allow composant to add JS/CSS
			do_action( 'sfield-admin-object-head', $post_type, $current_customtype );
			return true;
		}
		
		return false;
	}	
	
	/**
	 * Add a javascript function on footer for put value on hidden field and build preview.
	 * 
	 */
	function addSendToEditor() {
		?>
		<script type="text/javascript">
			// send html to the post editor
			function my_send_to_editor(h) {
				var datas = h.split('|');

				// Set attachment ID on hidden input
				jQuery('input[name='+datas[0]+']').val( datas[1] );

				// Use Ajax for load preview
				jQuery('#preview-'+datas[0]).load( '<?php echo admin_url('admin-ajax.php'); ?>?action=ct_preview_media', { 'preview_id_media': datas[1], 'field_name': datas[0] }  );

				// Close thickbox !
				tb_remove();
			}
		</script>
		<?php
	}
	
	/**
	 * Display a empty page with only JS and call parent function send to editor
	 *
	 */
	function checkMediaInsert() {
		echo '<script type="text/javascript">' . "\n";
			echo 'var win = window.dialogArguments || opener || parent || top;' . "\n";
			echo "win.my_send_to_editor('".addslashes($_GET['field'].'|'.$_GET['id'])."');" . "\n";
		echo '</script>' . "\n";
		die();
	}
	
	/**
	 * Add links on media management popup
	 * 
	 * @return string
	 */
	function addMediaLinksOnMeta( $media_dims, $attachment = null ) {
		if ( $attachment == false ) {
			return $media_dims;
		}
		
		if ( isset($_GET['post_id']) ) {
			$_post = get_post( $_GET['post_id'] );
		} else {
			$_post = get_post( $attachment->post_parent );	
		}
		
		if ( $_post == false ) {
			return $media_dims;
		}
		
		$post_type = $_post->post_type;
		if ( empty($post_type) ) {
			return $media_dims;
		}

		// Custom images on CS ?
		$current_options = get_option( SCUST_OPTION );

		// Custom taxo ?
		if ( !isset($current_options['customtypes'][$post_type]) ) {
			return $media_dims;
		}

		$current_customtype = $current_options['customtypes'][$post_type];
		if ( !is_array($current_customtype['custom']) || empty($current_customtype['custom']) ) { // Custom fields for this custom type ?
			return $media_dims;
		}
		
		// Flag type in Custom fields ?
		$fields = array();
		foreach( (array) $current_customtype['custom'] as $field ) {
			if( $field['type'] == 'image' || $field['type'] == 'media' ) {
				$fields[] = $field;
			}
		}
		
		if ( empty($fields) ) {
			return $media_dims;
		}
		
		$media_dims .= ' ';
		$media_dims .= '<script type="text/javascript">' . "\n";
			$media_dims .= 'jQuery(document).ready(function() {' . "\n";
				foreach( (array) $fields as $field ):
					$media_dims .= 'jQuery(\'input[name=send['.$attachment->ID.']]\').after(\'<a class="custom-add" style="margin:0 5px;" href="'.admin_url('admin-ajax.php').'?action=insert_custom_field_media&field='.esc_attr($field['name']).'&id='.$attachment->ID.'">'.esc_js(sprintf(__('Use for %s', 'simple-customtypes'), $field['label'])).'</a>\');' . "\n";
				endforeach;
			$media_dims .= '});' . "\n";
		$media_dims .= '</script>' . "\n";
	
		return $media_dims;
	}

	/**
	 * Save datas
	 * 
	 * @param $post_ID
	 * @param $post
	 * @return boolean
	 */
 	function saveCustomFields( $post_ID = 0, $post = null )  {
 		if ( $post->post_type != $this->pt->post_type) {
 			return false;
 		}
 		
		foreach( $this->pt->cf_registered_sidebars as $index => $_s){
			//var_dump($index);
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
				continue;
			
			$sidebar = $this->pt->cf_registered_sidebars[$index];
	
			$did_one = false;
			$params = array();
			$i = 0;
			
			
			foreach ( (array) $sidebars_fields[$index] as $id ) {
	
				if ( !isset($this->pt->cf_registered_fields[$id]) ) continue;
				
				//var_dump(array_keys($this->pt->cf_registered_fields[$id]));
				$number = current($this->pt->cf_registered_fields[$id]['params']);
				//var_dump($number['number']);
				$id_base = str_ireplace('_', '-', $this->pt->cf_registered_fields[$id]['classname']);
				//var_dump( $_POST[$id_base][$number['number']] );
				
				$field_name = isset($this->pt->cf_registered_fields[$id]['name']) ? $this->pt->cf_registered_fields[$id]['name'] : '';
				$field_params = isset($this->pt->cf_registered_fields[$id]['params']) ? $this->pt->cf_registered_fields[$id]['params'] : array();
				
				$entries = isset($_POST[$id_base][$number['number']]) ? $_POST[$id_base][$number['number']] : '';
				$params = array_merge( array( array_merge( $sidebar, array('field_id' => $id, 'field_name' => $field_name, 'entries' => $entries) ) ), (array) $field_params );
										
				$params[0]['post_id'] = $post->ID;
				$i = 1;
				$callback = $this->pt->cf_registered_fields[$id]['save_callback'];
				
				if ( is_callable( $callback ) ) {
					call_user_func_array( $callback, $params);
					$did_one = true;
				}
			}

		}
		return $did_one;

	}
	
	/**
	 * Check if post type is load ?
	 * 
	 * @param string $post_type
	 * @return boolean
	 */
	function initCustomFields( $post_type = '' ) {
		if ( isset($post_type) && !empty($post_type) && $post_type == $this->pt->post_type) {
			return $this->loadCustomFields( $post_type );
		}
		return false;
	}
	
	/**
	 * Group custom fields for build boxes.
	 * 
	 * @param $post_type
	 * @return boolean
	 */
	function loadCustomFields( $post_type = '' ) {
		//$index = 'top-sidebar-' . $post_type;
		foreach( $this->pt->cf_registered_sidebars as $index => $_s){
			if( $index == 'cf_inactive_fields' )
				continue;
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
			//var_dump($sidebars_fields);
			if ( empty($this->pt->cf_registered_sidebars[$index]) || !array_key_exists($index, $sidebars_fields) || !is_array($sidebars_fields[$index]) || empty($sidebars_fields[$index]) )
				continue;

			$sidebar = $this->pt->cf_registered_sidebars[$index];
	
			$did_one = false;
			$params = array();
			$i = 0;
			foreach ( (array) $sidebars_fields[$index] as $id ) {

				if ( !isset($this->pt->cf_registered_fields[$id]) ) continue;
	
				$params = array_merge(
					array( array_merge( $sidebar, array('field_id' => $id, 'field_name' => $this->pt->cf_registered_fields[$id]['name']) ) ),
					(array) $this->pt->cf_registered_fields[$id]['params']
				);

				$i = 1;
				
			}
			if( $i == 0 )
				continue;
			$p = current($params);
			add_meta_box($p['id'], $p['name'], array(&$this, 'genericRenderBoxes'), $post_type, 'advanced', 'default', array( $index ) );
		}
		return $did_one;
	}
	
	/**
	 * Generic boxes who allow to build xHTML for each box
	 * 
	 * @param $post
	 * @param $box
	 * @return boolean
	 */
	function genericRenderBoxes( $post = null, $box = null ) {
		$index = current($box['args']);
		$sidebars_fields = $this->pt->cf_field_sidebar->cf_get_sidebars_fields();
		$sidebar = $this->pt->cf_registered_sidebars[$index];
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
			$params[0]['post_id'] = $post->ID;
			//$params = apply_filters( 'dynamic_sidebar_params', $params );
			
			$callback = $this->pt->cf_registered_fields[$id]['callback'];
			do_action( 'dynamic_sidebar', $this->pt->cf_registered_fields[$id] );
			echo '<div class="container-sct form-wrap">';
			if ( is_callable( $callback ) ) {
				call_user_func_array( $callback, $params);
				$did_one = true;
			}
			echo '</div>';
		}
		return true;
	}
	
	/**
	 * Build HTML for preview the media
	 * 
	 * @param $current_value
	 * @param $field_name
	 * @return string
	 */
	function getPreviewMedia( $current_value = 0, $field_name = '' ) {
		$output = '';
		if ( (int) $current_value != 0 && $thumb_url = wp_get_attachment_image_src( $current_value ) ) {

			$post = get_post($current_value);
			$title = esc_attr($post->post_title);
			$title = ( !empty( $title ) ) ? $title : basename($post->guid);

			$output .= '<img src="'.$thumb_url[0].'" alt="'.esc_attr($title).'" style="float:left;margin:0 10px 0 0" />';
			$output .= wp_html_excerpt($title, 60);
			$output .= '<br /> <label><input type="checkbox" name="delete-'.esc_attr($field_name).'" class="delete-media" value="'.$current_value.'" /> '.__('Delete', 'simple-taxoxomy').'</label>' . "\n";
			$output .= '<div class="clear"></div>' . "\n";

		}

		return $output;
	}
	
	/**
	 * Allow to insert preview HTML with Ajax call.
	 * 
	 */
	function checkAjaxPreview() {
		echo $this->getPreviewMedia( (int) $_REQUEST['preview_id_media'], stripslashes($_REQUEST['field_name']) );
		exit();
	}
	
}
?>