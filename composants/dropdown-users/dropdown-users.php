<?php
class CF_Field_Dropdown_Users extends CF_Field{
	
	function CF_Field_Dropdown_Users(){
		
		$field_ops = array('classname' => 'field_dropdown', 'description' => __( 'Dropdown Users', 'custom-fields') );
		$this->CF_Field('dropdown', __('Dropdown Users', 'custom-fields'), 'input-dropdown', true, $field_ops);
	}
	
	function field( $args, $instance ) {
		global $current_user, $user_ID;
		extract( $args );
		
		$entries = is_array($entries) ? $entries['name'] : $entries;
		
		$title = apply_filters('widget_title', empty( $instance['title'] ) ? __( 'Editor' ) : $instance['title'], $instance, $this->id_base);
		
		echo $before_widget;
		if ( $title)
			echo $before_title . $title . $after_title;

			$authors = get_editable_user_ids( $current_user->id, true );
			if ( isset($entries) && !empty($entries) && !in_array($entries, $authors) )
				$authors[] = $entries;
			
			wp_dropdown_users( array('include' => $authors, 'name' => $this->get_field_name('name'), 'selected' => empty($entries) ? $user_ID : $entries) );

		echo $after_widget;
	}
	
	function save( $values ) {
		$values = $values['name'];
		return $values;
	}
	
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		return $instance;
	}

	function form( $instance ) {
		//Defaults
		$instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );
		?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'custom-fields'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
		</p>
		<?php
	}
}
?>