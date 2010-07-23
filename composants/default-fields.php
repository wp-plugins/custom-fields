<?php
class CF_Field_Textarea extends CF_Field{
	
	function CF_Field_Textarea(){
		$field_ops = array('classname' => 'field_textarea', 'description' => __( 'Block Text without editor') );
		$this->CF_Field('textarea', __('Textarea'), '_input-textarea', true, $field_ops);
	}
	
	function field( $args, $instance ) {
		extract( $args );

		$title = apply_filters('widget_title', empty( $instance['title'] ) ? __( 'Pages' ) : $instance['title'], $instance, $this->id_base);

			echo $before_widget;
			if ( $title)
				echo $before_title . $title . $after_title;
		?>
		<textarea name="<?php echo $this->get_field_name('name'); ?>" id="<?php echo $this->get_field_id('pages'); ?>" rows="5" cols="50"><?php echo esc_html($entries['name'])?></textarea>
		<?php if( isset($instance['description']) )?>
			<p><?php echo $instance['description']; ?></p>
		<?php
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
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'description' => '' ) );
		$title = esc_attr( $instance['title'] );
		$description = esc_html($instance['description']);
	?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></p>
		<p><label for="<?php echo $this->get_field_id('description'); ?>"><?php _e('Description:'); ?></label> <textarea name="<?php echo $this->get_field_name('description'); ?>" id="<?php echo $this->get_field_id('description'); ?>" cols="20" rows="4" class="widefat"><?php echo $description; ?></textarea></p>
<?php
	}
	
}


class CF_Field_EditorLight extends CF_Field{
	
	function CF_Field_EditorLight(){
		$field_ops = array('classname' => 'field_editorlight', 'description' => __( 'The Light Editor') );
		$this->CF_Field('editorlight', __('Editor Light'), '_input-editorlight', true, $field_ops);
		//add_action( 'admin_enqueue_scripts', array(&$this, 'initStyleScript'), 10 );
	}
	
	function field( $args, $instance ) {
		extract( $args );

		$title = apply_filters('widget_title', empty( $instance['title'] ) ? __( 'Editor Light' ) : $instance['title'], $instance, $this->id_base);

			echo $before_widget;
			if ( $title)
				echo $before_title . $title . $after_title;
		?>
		<div class="editor-light-field">
			<textarea class="mceEditor" name="<?php echo $this->get_field_name('name'); ?>" id="<?php echo $this->get_field_id('name'); ?>" rows="5" cols="50" style="width: 97%;">
				<?php echo esc_html($entries['name'])?>
			</textarea>
		</div>
		<?php if( isset($instance['description']) )?>
			<p><?php echo $instance['description']; ?></p>
		<?php
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
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'description' => '' ) );
		$title = esc_attr( $instance['title'] );
		$description = esc_html($instance['description']);
	?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></p>
		<p><label for="<?php echo $this->get_field_id('description'); ?>"><?php _e('Description:'); ?></label> <textarea name="<?php echo $this->get_field_name('description'); ?>" id="<?php echo $this->get_field_id('description'); ?>" cols="20" rows="4" class="widefat"><?php echo $description; ?></textarea></p>
<?php
	}
	
	
	/**
	 * Load JS and CSS need for admin features.
	 * 
	 */
	function initStyleScript() {

		add_action( 'admin_print_footer_scripts', 'wp_tiny_mce', 25 );
		add_action( 'admin_print_footer_scripts', array(&$this, 'customTinyMCE'), 26 );

	}
	
	/**
	 * Display TinyMCE JS for init light editor.
	 * 
	 */
	function customTinyMCE() {
		$mce_locale = ( '' == get_locale() ) ? 'en' : strtolower( substr(get_locale(), 0, 2) ); // only ISO 639-1
		$mce_spellchecker_languages = apply_filters('mce_spellchecker_languages', '+English=en,Danish=da,Dutch=nl,Finnish=fi,French=fr,German=de,Italian=it,Polish=pl,Portuguese=pt,Spanish=es,Swedish=sv');
		?>
		<script type="text/javascript">
		/* <![CDATA[ */
		tinyMCE.init({
			mode : "specific_textareas",
			editor_selector : "mceEditor",
			width:"100%", 
			theme:"advanced", 
		/*	elements : "<?php echo $this->get_field_id('name'); ?>",*/
			skin:"wp_theme", 
			theme_advanced_buttons1:"bold,italic,strikethrough,|,bullist,numlist,blockquote,|,justifyleft,justifycenter,justifyright,|,link,unlink,wp_more,|,spellchecker,fullscreen,wp_adv", 	
			theme_advanced_buttons2:"formatselect,underline,justifyfull,forecolor,|,pastetext,pasteword,removeformat,|,media,charmap,|,outdent,indent,|,undo,redo,wp_help,|,code", 	
			theme_advanced_buttons3:"",
			theme_advanced_buttons4:"", 
			language:"<?php echo $mce_locale; ?>",
			spellchecker_languages:"<?php echo $mce_spellchecker_languages; ?>",
			theme_advanced_toolbar_location:"top", 
			theme_advanced_toolbar_align:"left", 
			theme_advanced_statusbar_location:"bottom", 
			theme_advanced_resizing:"1", 
			theme_advanced_resize_horizontal:"", 
			dialog_type:"modal", 
			relative_urls:"", 
			remove_script_host:"", 
			convert_urls:"", 
			apply_source_formatting:"", 
			remove_linebreaks:"1", 
			gecko_spellcheck:"1", 
			entities:"38,amp,60,lt,62,gt", 
			accessibility_focus:"1", 
			tabfocus_elements:"major-publishing-actions", 
			media_strict:"", 
			paste_remove_styles:"1", 
			paste_remove_spans:"1", 
			paste_strip_class_attributes:"all", 
			wpeditimage_disable_captions:"", 
			plugins:"safari,inlinepopups,spellchecker,paste,wordpress,media,fullscreen,wpeditimage,wpgallery,tabfocus"
		});
		/* ]]> */
		</script>
		<?php
	}
	
}

class CF_Field_Editor extends CF_Field{
	
	function CF_Field_Editor(){
		$field_ops = array('classname' => 'field_editor', 'description' => __( 'Editor') );
		$this->CF_Field('editor', __('Editor'), '_input-editor', true, $field_ops);
	}
	
	function field( $args, $instance ) {
		extract( $args );

		$title = apply_filters('widget_title', empty( $instance['title'] ) ? __( 'Editor' ) : $instance['title'], $instance, $this->id_base);


		echo $before_widget;
		if ( $title)
			echo $before_title . $title . $after_title;

		the_editor( $entries['name'], $this->get_field_name('name'), 'title', false, 10 );

		echo $after_widget;

	}
	
	function save( $values ){
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
		$title = esc_attr( $instance['title'] );
	?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></p>
<?php
	}
	
}


class CF_Field_Select extends CF_Field{
	
	function CF_Field_Select(){
		$field_ops = array('classname' => 'field_select', 'description' => __( 'Select') );
		$this->CF_Field('select', __('Select'), '_input-select', true, $field_ops);
	}
	
	function field( $args, $instance ) {
		extract( $args );
		
		$values = array();
		$v = explode('#', $instance['settings']);
		
		//$ti = array_shift($v);
		
		foreach($v as $val){
			$a = explode('|', $val);
			$values[$a[0]] = $a[1];
		}
		$title = apply_filters('widget_title', empty( $instance['title'] ) ? __( 'Editor' ) : $instance['title'], $instance, $this->id_base);



		echo $before_widget;
		if ( $title)
			echo $before_title . $title . $after_title;
		echo '<div class="select-field">';
		echo '<select name="'.$this->get_field_name('name').'" id="'.$this->get_field_id('name').'" style="width: 47%;">';
			foreach( (array) $values as $key => $val ) {
				echo '<option value="'.esc_attr($val).'" '.selected($val, $entries['name'], false).'>'.esc_html(ucfirst($key)).'</option>' . "\n";
			}
		echo '</select>' . "\n";
		echo '</div>';
		if( $instance['description'] != '' )
			echo '<p>' . $instance['description'] . '</p>';
		echo $after_widget;

	}
	
	function save( $values ){
		return $values;
	}
	
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$instance['title'] = strip_tags($new_instance['title']);
		$instance['settings'] = strip_tags($new_instance['settings']);
		$instance['description'] = strip_tags($new_instance['description']);
		return $instance;
	}

	function form( $instance ) {
		//Defaults
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'settings' => '' ) );
		$title = esc_attr( $instance['title'] );
		$settings = esc_attr( $instance['settings'] );
		$description = esc_html($instance['description']);
	?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></p>
		
		<p><label for="<?php echo $this->get_field_id('settings'); ?>"><?php _e('Settings:'); ?></label> 
		<textarea class="widefat" id="<?php echo $this->get_field_id('settings'); ?>" name="<?php echo $this->get_field_name('settings'); ?>" ><?php echo $settings; ?></textarea>
		<br/><small>Parameters like : label1|id1#label2|id2</small></p>
		<p><label for="<?php echo $this->get_field_id('description'); ?>"><?php _e('Description:'); ?></label> <textarea name="<?php echo $this->get_field_name('description'); ?>" id="<?php echo $this->get_field_id('description'); ?>" cols="20" rows="4" class="widefat"><?php echo $description; ?></textarea></p>
<?php
	}
	
}

class CF_Field_SelectMultiple extends CF_Field{
	
	function CF_Field_SelectMultiple(){
		$field_ops = array('classname' => 'field_selectmultiple', 'description' => __( 'Select Mutiple') );
		$this->CF_Field('selectmultiple', __('Select Multiple'), '_input-selectmultiple', true, $field_ops);
	}
	
	function field( $args, $instance ) {
		extract( $args );
		
		$values = array();
		$v = explode('#', $instance['settings']);
		
		//$ti = array_shift($v);
		
		foreach($v as $val){
			if( is_numeric( strpos($val, '|') ) ){
				$a = explode('|', $val);
				$values[$a[0]] = $a[1];
			}
		}
		$title = apply_filters('widget_title', empty( $instance['title'] ) ? __( 'Editor' ) : $instance['title'], $instance, $this->id_base);



		echo $before_widget;
		if ( $title)
			echo $before_title . $title . $after_title;
		echo '<div class="select_multiple_field">';
		echo '<select name="'.$this->get_field_name('name').'[]" id="'.$this->get_field_id('name').'" style="height:200px;" multiple>';
			foreach( (array) $values as $key => $val ) {
				echo '<option value="'.esc_attr($val).'" '.selected(true, in_array($val, $entries['name']), false).'>'.esc_html(ucfirst($key)).'</option>' . "\n";
			}
		echo '</select>' . "\n";
		echo '</div>';
		if( $instance['description'] != '' )
			echo '<p>' . $instance['description'] . '</p>';
		echo $after_widget;

	}
	
	function save( $values ){
		return $values;
	}
	
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		//var_dump($new_instance);
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['settings'] = strip_tags($new_instance['settings']);
		$instance['description'] = strip_tags($new_instance['description']);
		return $instance;
	}

	function form( $instance ) {
		//Defaults
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'settings' => '' ) );

		$title = esc_attr( $instance['title'] );
		$settings = esc_attr( $instance['settings'] );
		$description = esc_html($instance['description']);
	?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></p>
		
		<p><label for="<?php echo $this->get_field_id('settings'); ?>"><?php _e('Settings:'); ?></label> 
		<textarea class="widefat" id="<?php echo $this->get_field_id('settings'); ?>" name="<?php echo $this->get_field_name('settings'); ?>" ><?php echo $settings; ?></textarea>
		<br/><small>Parameters like : label1|id1#label2|id2</small></p>
		<p><label for="<?php echo $this->get_field_id('description'); ?>"><?php _e('Description:'); ?></label> <textarea name="<?php echo $this->get_field_name('description'); ?>" id="<?php echo $this->get_field_id('description'); ?>" cols="20" rows="4" class="widefat"><?php echo $description; ?></textarea></p>
<?php
	}
	
}


class CF_Field_Checkbox extends CF_Field{
	
	function CF_Field_Checkbox(){
		$field_ops = array('classname' => 'field_checkbox', 'description' => __( 'Checkbox') );
		$this->CF_Field('checkbox', __('Checkbox'), '_input-checkbox', true, $field_ops);
	}
	
	function field( $args, $instance ) {
		extract( $args );
		
		$values = array();
		$v = explode('#', $instance['settings']);
		//$ti = array_shift($v);
		if( empty($v))
			return false;
		foreach($v as $val){
			$a = explode('|', $val);
			if( count($a) != 2)
				continue;
			$values[$a[0]] = $a[1];
		}
		if(empty($values))
			return false;
			
		$title = apply_filters('widget_title', empty( $instance['title'] ) ? __( 'Editor' ) : $instance['title'], $instance, $this->id_base);



		echo $before_widget;
		if ( $title)
			echo $before_title . $title . $after_title;
		echo '<div class="checkbox-field">';
		//echo '<select name="'.$this->get_field_name('name').'[]" id="'.$this->get_field_id('name').'" style="width: 47%;height:200px;" multiple>';
			foreach( (array) $values as $key => $val ) {
				echo '<label><input type="checkbox" name="'.$this->get_field_name('name').'[]" id="'.$this->get_field_id('name').'" value="'.esc_attr($val).'" '.checked(true, in_array($val, (array)$entries['name']), false).'/> '.$key.'</label>' . "\n";
			}
		//echo '</select>' . "\n";
		echo '</div>';
		if( $instance['description'] != '' )
			echo '<p>' . $instance['description'] . '</p>';
		echo $after_widget;

	}
	
	function save( $values ){
		return $values;
	}
	
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		//var_dump($new_instance);
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['settings'] = strip_tags($new_instance['settings']);
		$instance['description'] = strip_tags($new_instance['description']);
		return $instance;
	}

	function form( $instance ) {
		//Defaults
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'settings' => '', 'description' => '' ) );

		$title = esc_attr( $instance['title'] );
		$settings = esc_attr( $instance['settings'] );
		$description = esc_html($instance['description']);
	?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></p>
		
		<p><label for="<?php echo $this->get_field_id('settings'); ?>"><?php _e('Settings:'); ?></label> 
		<textarea class="widefat" id="<?php echo $this->get_field_id('settings'); ?>" name="<?php echo $this->get_field_name('settings'); ?>" ><?php echo $settings; ?></textarea>
		<br/><small>Parameters like : label1|id1#label2|id2</small>
		</p>
		<p><label for="<?php echo $this->get_field_id('description'); ?>"><?php _e('Description:'); ?></label> <textarea name="<?php echo $this->get_field_name('description'); ?>" id="<?php echo $this->get_field_id('description'); ?>" cols="20" rows="4" class="widefat"><?php echo $description; ?></textarea></p>
<?php
	}
	
}


class CF_Field_Input extends CF_Field{
	
	function CF_Field_Input(){
		$field_ops = array('classname' => 'field_inputtext', 'description' => __( 'Input Text') );
		$this->CF_Field('inputtext', __('Input Text'), 'input-inputtext', true, $field_ops);
	}
	
	function field( $args, $instance ) {
		extract( $args );
		
		$title = apply_filters('widget_title', empty( $instance['title'] ) ? __( 'Editor' ) : $instance['title'], $instance, $this->id_base);

		echo $before_widget;
		if ( $title)
			echo $before_title . $title . $after_title;
		
		echo '<input type="text" name="'.$this->get_field_name('name').'" id="'.$this->get_field_id('name').'" value="'.esc_attr($entries['name']).'"/> ';
		if( $instance['description'] != '' )
			echo '<p>' . $instance['description'] . '</p>';
		
		echo $after_widget;
	}
	
	function save( $values ){
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

class CF_Field_Media extends CF_Field{
	
	function CF_Field_Media(){
		$field_ops = array('classname' => 'field_simplemedia', 'description' => __('Simple Media', 'simple-media-widget') );
		$this->CF_Field('simplemedia', __('Simple Media', 'simple-media-widget'), 'simplemedia', true, $field_ops);
	}
	
	function field( $args, $instance ) {
		extract( $args );
		
		$title 		= apply_filters('widget_title', empty($instance['title']) ? '' : $instance['title']);
		// Target link ?
		$link_target =  ( $link_target == __('New tab', 'simple-media-widget') ) ? ' target="_blank"' : '';
		
		echo $before_widget;
			echo $before_title . $title . $after_title;
			
			
			$media_id 	= intval	($entries['media_id']);
			
			$medias = get_posts( array('numberposts' => 9999999, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => $instance['type'], 'orderby' => 'menu_order ASC, ID', 'order' => 'DESC') );
			if ( $medias == false ) {
				$medias = array();
			}
			
			// Add no media to array
			$no_media = new StdClass;
			$no_media->ID = 0;
			$no_media->post_title = __('None', 'simple-media-widget');
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
		return $values;
	}
	
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		
		$instance['title'] 		= strip_tags($new_instance['title']);
		$instance['type'] 		= strip_tags($new_instance['type']);
		$instance['description'] = strip_tags($new_instance['description']);
		
		return $instance;
	}

	function form( $instance ) {
		//Defaults
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'type' => 'all' ) );

		$title = esc_attr( $instance['title'] );
		$type = $instance['type'];
		$description = esc_html($instance['description']);
	?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></p>
		<p>
			<label for="<?php echo $this->get_field_id('type'); ?>"><?php _e('Type of files:'); ?></label>
			<select class="widefat" id="<?php echo $this->get_field_id('type'); ?>" name="<?php echo $this->get_field_name('type'); ?>">
				<option value="all" <?php selected('all', $type);?>><?php _e('All'); ?></option>
				<option value="image" <?php selected('image', $type);?>><?php _e('All Images'); ?></option>
				<?php foreach( get_available_post_mime_types('attachment') as $mime_type ): ?>
				<option value="<?php echo esc_attr($mime_type);?>" <?php selected($mime_type, $type);?>><?php echo esc_html($mime_type);?></option>
				<?php endforeach;?>
			</select>
		</p>
		<p><label for="<?php echo $this->get_field_id('description'); ?>"><?php _e('Description:'); ?></label> <textarea name="<?php echo $this->get_field_name('description'); ?>" id="<?php echo $this->get_field_id('description'); ?>" cols="20" rows="4" class="widefat"><?php echo $description; ?></textarea></p>
<?php
	}
	
}