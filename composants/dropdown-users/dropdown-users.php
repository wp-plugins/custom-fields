<?php
class CF_Field_Dropdown_Users extends CF_Field{
	
	function CF_Field_Dropdown_Users(){
		
		$field_ops = array('classname' => 'field_dropdown', 'description' => __( 'Dropdown Users') );
		$this->CF_Field('dropdown', __('Dropdown Users'), 'input-dropdown', true, $field_ops);
	}
	
	function field( $args, $instance ) {
		global $current_user, $user_ID;
		extract( $args );

		$title = apply_filters('widget_title', empty( $instance['title'] ) ? __( 'Editor' ) : $instance['title'], $instance, $this->id_base);
		
		echo $before_widget;
		if ( $title)
			echo $before_title . $title . $after_title;

			$authors = get_editable_user_ids( $current_user->id, true );
			if ( isset($entries['name']) && !empty($entries['name']) && !in_array($entries['name'], $authors) )
				$authors[] = $entries['name'];
			
			wp_dropdown_users( array('include' => $authors, 'name' => $this->get_field_name('name'), 'selected' => empty($entries['name']) ? $user_ID : $entries['name']) );

		echo $after_widget;
	}
	
	function save( $values ) {
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
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
		</p>
		<?php
	}
}
?>