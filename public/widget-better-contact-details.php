<?php
/*
 * Better Contact Details widget
 * @since 1.0.0
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*
 * Extend the standard WP_Widget class
 */

class Better_Contact_Details_Widget extends WP_Widget {
	
	private $settings;
	/**
	 * Sets up the widgets name etc
	 */
	public function __construct() {
		$widget_ops = array( 
			'classname' => 'better_contact_details_widget',
			'description' => 'Add your contact details',
		);
		parent::__construct( 'better_contact_details_widget', 'Contact Details', $widget_ops );

		$this -> settings = bcd_get_settings();
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		
		// outputs the content of the widget
		$format = isset ( $instance['format'] ) ? $instance['format'] : 'vertical';
		$title = ( ! empty( $instance['title'] ) ) ? $instance['title'] : '';

		/** This filter is documented in wp-includes/widgets/class-wp-widget-pages.php */
		$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );
		
		$contacts = array();
		$contacts['format'] = $format;
		
		foreach ( $this -> settings as $setting ) {
			if ( $setting['section_id'] == 'standard_details' || $setting['section_id'] == 'social_details' ) {
				foreach ( $setting['fields'] as $field ) {
					if ( isset ( $instance[$field['id']] ) && $instance[$field['id']] === 1 ) {
						$contacts['fields'][] = $field['id'];
					}
				}
			}
		}
		
		// Display contact details
		if ( function_exists ( 'bcd_contact_details' ) ) {
			echo $args['before_widget'];
			if ( $title ) {
				echo $args['before_title'] . $title . $args['after_title'];
			}
			bcd_contact_details ( $contacts );
			echo $args['after_widget'];
		}
		
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {

		$title = isset( $instance['title'] ) ? $instance['title'] : '';
		$format = isset ( $instance['format'] ) ? $instance['format'] : 'vertical'; ?>
		
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id( 'format' ); ?>"><?php _e( 'List style', 'better-contact-details' ); ?></label>
			
			<select id="<?php echo $this->get_field_id ( 'format' ); ?>" name="<?php echo $this->get_field_name ( 'format' ); ?>">
				<option value="horizontal" <?php selected ( 'horizontal', $format ); ?>><?php _e( 'Horizontal', 'better-contact-details' ); ?></option>
				<option value="vertical" <?php selected ( 'vertical', $format ); ?>><?php _e( 'Vertical', 'better-contact-details' ); ?></option>
			</select>
		</p>

		<?php 
		
		foreach ( $this -> settings as $setting ) {
			if ( $setting['section_id'] == 'standard_details' || $setting['section_id'] == 'social_details' ) {
				foreach ( $setting['fields'] as $field ) {
					$option = $field['id'];
					$value = isset ( $instance[$field['id']] ) ? $instance[$field['id']] : 0; ?>
					<p>
						<input id="<?php echo $this->get_field_id ( $option ); ?>" name="<?php echo $this->get_field_name ( $option ); ?>" <?php checked( $value, 1, true ); ?> type="checkbox" value="1" />
						<label for="<?php echo $this->get_field_id( $option ); ?>"><?php echo $field['label']; ?></label>
					</p>
		
			<?php }
			}
		}
		
	}
	

	/**
	 * Processing widget options on save
	 *
	 * @param array $new_instance The new options
	 * @param array $old_instance The previous options
	 */
	public function update( $new_instance, $old_instance ) {
		// processes widget options to be saved
		$instance = $old_instance;
		$instance['title'] 	= sanitize_text_field( $new_instance['title'] );
		$instance['format'] = isset ( $new_instance['format'] ) ? $new_instance['format'] : 'vertical';
		
		foreach ( $this -> settings as $setting ) {
			if ( $setting['section_id'] == 'standard_details' || $setting['section_id'] == 'social_details' ) {
				foreach ( $setting['fields'] as $field ) {
					$option = $field['id'];
					$instance[$option] = $new_instance[$option] ? 1 : 0;
				}
			}
		}

		return $instance;
	}
	
}