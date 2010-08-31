<?php
class CF_Field_Media extends CF_Field{
	
	function CF_Field_Media(){
		$field_ops = array('classname' => 'field_simplemedia', 'description' => __('Simple Media', 'custom-fields') );
		$this->CF_Field('simplemedia', __('Simple Media', 'custom-fields'), 'simplemedia', true, $field_ops);
	}
	
	function field( $args, $instance ) {
		global $post;
		extract( $args );
		
		$title 		= apply_filters('widget_title', empty($instance['title']) ? '' : $instance['title']);
		// Target link ?
		$link_target =  ( $link_target == __('New tab', 'custom-fields') ) ? ' target="_blank"' : '';
		
		echo $before_widget;
			echo $before_title . $title . $after_title;
			
			$entries = is_array($entries) ? $entries['media_id'] : $entries;
			
			$media_id 	= intval	($entries);
			
			$type = $instance['type'] == 'all' ? null : $instance['type'];
			
			$post_parent = (isset($instance['alltype']) || $post->ID == 0) ? null : $post->ID;
			
			$medias = get_posts( array('numberposts' => 9999999, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => $type, 'orderby' => 'menu_order ASC, ID', 'order' => 'DESC', 'post_parent' => $post_parent) );
			if ( $medias == false ) {
				$medias = array();
			}
			
			// Add no media to array
			$no_media = new StdClass;
			$no_media->ID = 0;
			$no_media->post_title = __('None', 'custom-fields');
			array_unshift( $medias, $no_media );
			unset($no_media);
			?>
			<div class="media-field">
				<select class="widefat" id="<?php echo $this->get_field_id('media_id'); ?>" name="<?php echo $this->get_field_name('media_id'); ?>">
					<?php foreach( (array) $medias as $media ) : ?>
						<option <?php if ( $media->ID == $media_id ) echo 'selected="selected"'; ?> value="<?php echo $media->ID; ?>"><?php echo esc_html($media->post_title); ?></option>
					<?php endforeach; ?>
				</select>
			</div>

			<?php
			if( $instance['description'] != '' )
				echo '<p>' . $instance['description'] . '</p>';
		echo $after_widget;
		
		return true;
	}
	
	function save( $values ){
		$values = $values['media_id'];
		return $values;
	}
	
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		
		$instance['title'] 			= strip_tags($new_instance['title']);
		$instance['type'] 			= strip_tags($new_instance['type']);
		$instance['description'] 	= strip_tags($new_instance['description']);
		$instance['allfiles'] 		= isset( $new_instance['allfiles'] ) ? 1 : 0;
		
		return $instance;
	}

	function form( $instance ) {
		//Defaults
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'type' => 'all', 'allfiles' => false ) );

		$title = esc_attr( $instance['title'] );
		$type = $instance['type'];
		$description = esc_html($instance['description']);
		?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'custom-fields'); ?></label> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></p>
		<p>
			<label for="<?php echo $this->get_field_id('type'); ?>"><?php _e('Type of files:', 'custom-fields'); ?></label>
			<select class="widefat" id="<?php echo $this->get_field_id('type'); ?>" name="<?php echo $this->get_field_name('type'); ?>">
				<option value="all" <?php selected('all', $type);?>><?php _e('All', 'custom-fields'); ?></option>
				<option value="image" <?php selected('image', $type);?>><?php _e('All Images', 'custom-fields'); ?></option>
				<?php foreach( get_available_post_mime_types('attachment') as $mime_type ): ?>
				<option value="<?php echo esc_attr($mime_type);?>" <?php selected($mime_type, $type);?>><?php echo esc_html($mime_type);?></option>
				<?php endforeach;?>
			</select>
		</p>
		<p>
			<input id="<?php echo $this->get_field_id('allfiles'); ?>" name="<?php echo $this->get_field_name('allfiles'); ?>" type="checkbox" <?php checked($instance['allfiles'], true); ?> />
			<label for="<?php echo $this->get_field_id('allfiles'); ?>"><?php _e('Show all medias in WP', 'custom-fields'); ?></label> 
		</p>
		<p><label for="<?php echo $this->get_field_id('description'); ?>"><?php _e('Description:', 'custom-fields'); ?></label> <textarea name="<?php echo $this->get_field_name('description'); ?>" id="<?php echo $this->get_field_id('description'); ?>" cols="20" rows="4" class="widefat"><?php echo $description; ?></textarea></p>
		<?php
	}
	
}
?>