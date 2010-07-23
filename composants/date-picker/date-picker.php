<?php
class CF_Field_DatePicker extends CF_Field{
	
	function CF_Field_DatePicker(){
		add_action( 'admin_enqueue_scripts', array(&$this, 'add_js'), 10, 4 );
		
		$field_ops = array('classname' => 'field_datepicker', 'description' => __( 'Date Picker') );
		$this->CF_Field('datepicker', __('Date Picker'), 'input-datepicker', true, $field_ops);
	}
	
	function field( $args, $instance ) {
		extract( $args );
		
		$title = apply_filters('widget_title', empty( $instance['title'] ) ? __( 'Editor' ) : $instance['title'], $instance, $this->id_base);
		
		echo $before_widget;
		if ( $title)
			echo $before_title . $title . $after_title;

		echo '<input id="'.$this->get_field_id('name').'" name="'.$this->get_field_name('name').'" type="text" value="'.esc_attr($entries['name']).'" size="30" />' . "\n";
	
		echo '<script type="text/javascript">' . "\n";
			echo 'jQuery(document).ready(function(){' . "\n";
				echo 'jQuery.datepicker.setDefaults( jQuery.datepicker.regional["fr"] );' . "\n";
				echo 'jQuery("#'.$this->get_field_id('name').'").datepicker({ dateFormat: "yy-mm-dd" });' . "\n";
			echo '});' . "\n";
		echo '</script>' . "\n";
		if( isset($instance['description']) )
			echo '<p>'. $instance['description'] .'</p>';
		echo $after_widget;

	}
	
	function save( $values ){
		return $values;
	}
	
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['description'] = strip_tags($new_instance['description']);
		return $instance;
	}

	function form( $instance ) {
		//Defaults
		$instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );
		$description = esc_html($instance['description']);
		$title = esc_attr( $instance['title'] );

	?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></p>
		<p><label for="<?php echo $this->get_field_id('description'); ?>"><?php _e('Description:'); ?></label> <textarea name="<?php echo $this->get_field_name('description'); ?>" id="<?php echo $this->get_field_id('description'); ?>" cols="20" rows="4" class="widefat"><?php echo $description; ?></textarea></p>

<?php
	}
	
	function add_js( $pagenow = '', $current_taxo = array(), $flag_editor = false, $flag_media = false ) {
		// Datepicker in Custom fields ?

		wp_enqueue_script('jquery-ui-datepicker',    SCF_URL . '/composants/date-picker/js/jquery-ui-1.7.2.custom.min.js', array('jquery'), '1.7.2' );
		wp_enqueue_script('jquery-ui-datepicker-fr', SCF_URL . '/composants/date-picker/js/ui.datepicker-fr.js', array('jquery-ui-datepicker'), '1.7.2' );
		
		wp_enqueue_style('jquery-ui-datepicker', 	 SCF_URL . '/composants/date-picker/css/smoothness/jquery-ui-1.7.2.custom.css', array(), '1.7.2', 'all');
	}
	
}
?>