<?php
class CF_Field_Input extends CF_Field{
	
	function CF_Field_Input(){
		$field_ops = array('classname' => 'field_inputtext', 'description' => __( 'Input Text') );
		$this->CF_Field('inputtext', __('Input Text'), 'input-inputtext', true, $field_ops);
	}
	
	function field( $args, $instance ) {
		extract( $args );
		
		$entries = is_array($entries) ? $entries['name'] : $entries;
		
		$title = apply_filters('widget_title', empty( $instance['title'] ) ? __( 'Editor' ) : $instance['title'], $instance, $this->id_base);

		echo $before_widget;
		if ( $title)
			echo $before_title . $title . $after_title;
		
		echo '<input type="text" name="'.$this->get_field_name('name').'" id="'.$this->get_field_id('name').'" value="'.esc_attr($entries).'"/> ';
		if( $instance['description'] != '' )
			echo '<p>' . $instance['description'] . '</p>';
		
		echo $after_widget;
	}
	
	function save( $values ){
		$values = $values['name'];
		return $values;
	}
	
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		//var_dump($new_instance);
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['description'] = strip_tags($new_instance['description']);
		return $instance;
	}

	function form( $instance ) {
		//Defaults
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'description' => '' ) );
		$description = esc_html($instance['description']);
		$title = esc_attr( $instance['title'] );
		?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></p>
		<p><label for="<?php echo $this->get_field_id('description'); ?>"><?php _e('Description:'); ?></label> <textarea name="<?php echo $this->get_field_name('description'); ?>" id="<?php echo $this->get_field_id('description'); ?>" cols="20" rows="4" class="widefat"><?php echo $description; ?></textarea></p>
		<?php
	}
	
}
?>