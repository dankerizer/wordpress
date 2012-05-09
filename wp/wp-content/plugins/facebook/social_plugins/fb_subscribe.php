<?php
function fb_get_subscribe_button($options = array()) {
	$params = '';
	
	foreach ($options as $option => $value) {
		$params .= $option . '="' . $value . '" ';
	}
	
	return '<div class="fb-subscribe" ' . $params . '></div>';
}

function fb_subscribe_button_automatic($content) {
	global $wpdb;
	$options = get_option('fb_options');
	
	foreach($options['subscribe'] as $param => $val) {
		$param = str_replace('_', '-', $param);
			
		$options['subscribe']['data-' . $param] =  $val;
	}
	
	$table_name = $wpdb->prefix . "fb_users";
	
	$fb_username = $wpdb->get_var($wpdb->prepare("SELECT fb_username FROM $table_name WHERE wp_uid = %d", get_the_author_meta('ID')));
	
	$options['subscribe']['data-href'] = 'http://www.facebook.com/' . $fb_username;
	
	$content .= fb_get_subscribe_button($options['subscribe']);
	
	return $content;
}


/**
 * Adds the Subscribe Button Social Plugin as a WordPress Widget
 */
class Facebook_Subscribe_Button extends WP_Widget {

	/**
	 * Register widget with WordPress
	 */
	public function __construct() {
		parent::__construct(
	 		'fb_subscribe', // Base ID
			'Facebook Subscribe Button', // Name
			array( 'description' => __( "Lets a user subscribe to your public updates on Facebook.", 'text_domain' ), ) // Args
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		extract( $args );

		echo $before_widget;

		//$options = array('data-href' => $instance['url']);
		
		echo fb_get_subscribe_button($instance);
		echo $after_widget;
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		
		$fields = fb_get_subscribe_button_fields_array();
		
		foreach ($fields['children'] as $field) {
			if (isset($new_instance[$field['name']])) {
				$instance[$field['name']] = $new_instance[$field['name']];
			}
		}

		return $instance;
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		fb_get_subscribe_button_fields('widget', $this);
	}
}


function fb_get_subscribe_button_fields($placement = 'settings', $object = null) {
	$fields_array = fb_get_subscribe_button_fields_array();
	
	fb_construct_fields($placement, $fields_array['children'], $fields_array['parent'], $object);
}

function fb_get_subscribe_button_fields_array() {
	$array['parent'] = array('name' => 'subscribe',
									'field_type' => 'checkbox',
									'help_text' => 'Click to learn more.',
									'help_link' => 'https://developers.facebook.com/docs/reference/plugins/subscribe/',
									);
	
	$array['children'] = array(array('name' => 'layout',
													'field_type' => 'dropdown',
													'options' => array('standard', 'button_count', 'box_count'),
													'help_text' => 'Determines the size and amount of social context at the bottom.',
													),
										array('name' => 'width',
													'field_type' => 'text',
													'help_text' => 'The width of the plugin, in pixels.',
													),
										array('name' => 'show_faces',
													'field_type' => 'checkbox',
													'help_text' => 'Show profile pictures below the button.  Applicable to standard layout only.',
													),
										array('name' => 'colorscheme',
													'field_type' => 'dropdown',
													'options' => array('light', 'dark'),
													'help_text' => 'The color scheme of the plugin.',
													),
										array('name' => 'font',
													'field_type' => 'dropdown',
													'options' => array('arial', 'lucida grande', 'segoe ui', 'tahoma', 'trebuchet ms', 'verdana'),
													'help_text' => 'The font of the plugin.',
													),
										);
	
	return $array;
}

?>