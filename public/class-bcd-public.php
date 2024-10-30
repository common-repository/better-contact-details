<?php
/*
 * Better Contact Details public class
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Plugin public class
 **/
if ( ! class_exists( 'BCD_Public' ) ) {

	class BCD_Public {
		
		public function __construct() {
		}
		
		/*
		 * Initialize the class and start calling our hooks and filters
		 * @since 1.0.0
		 */
		public function init() {
			add_action( 'customize_register', array ( $this, 'register_customizer' ), 10, 1 );
			add_action( 'wp_enqueue_scripts', array ( $this, 'enqueue_scripts' ) );
			add_action( 'wp_head', array ( $this, 'inline_styles' ) );
			add_shortcode( 'contact_details', array ( $this, 'contact_details_shortcode' ) );
		}
		
		/*
		 * Initialize the class and start calling our hooks and filters
		 * @since 1.0.0
		 */
		public function enqueue_scripts() {
			$general = get_option ( 'settings_details_settings' );
			$font_awesome = ! empty ( $general['enqueue_font_awesome'] ) ? 1 : 0;
			$hide_labels = ! empty ( $general['hide_labels'] ) ? 1 : 0;
			
			if ( ! empty ( $font_awesome ) || ! empty ( $hide_labels ) ) {
				wp_enqueue_style ( 'font-awesome', BCD_PLUGIN_URL . 'assets/fonts/font-awesome/css/font-awesome.min.css', array(), '4.6.3' );
			}
		}
		
		/*
		 * Add some style...
		 * @since 1.0.0
		 */
		public function inline_styles() {
			
			$general = get_option ( 'settings_details_settings' );

			$use_styles = ! empty ( $general['add_styles'] ) ? 1 : 0;
			
			if ( true == $use_styles ) { ?>
				<style type="text/css" id="bcd-styles">
					ul.bcd-contact-list {
						list-style: none;
						margin: 0;
						padding: 0
					}
					ul.bcd-contact-list.has-icons li {
						padding-left: 1.5em;
						position: relative;
					}
					ul.bcd-contact-list.horizontal-list li {
						float: left;
					}
					ul.bcd-contact-list.has-icons li .fa {
						position: absolute;
						top: 0;
						left: 0;
						line-height: inherit;
					}
					ul.bcd-contact-list a {
						box-shadow: none;
					}
				</style>
			<?php }
		}
		
		/*
		 * Register the customizer
		 * @since 1.0.0
		 */
		public function register_customizer ( $wp_customize ) {
			
			// Get all options as array so they can be shared with settings panel in admin
			$settings = bcd_get_settings();	
			
			if ( ! empty ( $settings ) ) {
				
				// Create the section
				$wp_customize -> add_panel (
					'contact_details_panel',
					array (
						'title'			=>	__( 'Contact Details', 'better-contact-details' ),
						'capability'	=> 'edit_theme_options',
						'priority'		=>  900 // @todo set this in options or apply_filters?
					)
				);
				
				foreach ( $settings as $setting ) {
					
					// Create the section
					$wp_customize -> add_section (
						$setting['section_id'] . '_settings', // These sections map to the tabs in the Settings
						array (
							'title'		=>	$setting['section_title'],
							'priority'	=>  $setting['priority'],
							'panel'		=>	'contact_details_panel'
						)
					);
						
					foreach ( $setting['fields'] as $field ) {
						$setting_args = array (
							'type' 					=> 'option', // or 'option'
							'capability' 			=> 'edit_theme_options',
							'default' 				=> '',
							'transport'				=> 'refresh', // or postMessage
						);
						if ( ! empty ( $field['sanitize'] ) ) {
							$setting_args['sanitize_callback'] = array ( $this, $field['sanitize'] );
						}
						$wp_customize -> add_setting (
							$setting['section_id'] . '_settings[' . $field['id'] . ']',
							$setting_args
						);
						$wp_customize -> add_control (
							$setting['section_id'] . '_settings[' . $field['id'] . ']',
							array(
								'type' 					=> $field['type'],
								'priority' 				=> $field['priority'], // Within the section.
								'section' 				=> $setting['section_id'] . '_settings', // Required, core or custom.
								'label' 				=> $field['label'],
								'choices'				=> $field['choices'],
								'description' 			=> $field['desc']
							)
						);
						
					}
				
				}
				
			}
				
		}
		
		/*
		 * Sanitize text input
		 * @since 1.0.0
		 */
		public function sanitize_text ( $value ) {
			return sanitize_text_field ( $value );
		}
		
		/*
		 * Sanitize textarea input
		 * @since 1.0.0
		 */
		public function sanitize_textarea ( $value ) {
			return wp_kses ( $value, array ( 'br' => array() ) );
		}
		
		/*
		 * Sanitize email addresses
		 * @since 1.0.0
		 */
		public function sanitize_email ( $value ) {
			return sanitize_email ( $value );
		}
		
		/*
		 * Sanitize urls
		 * @since 1.0.0
		 */
		public function sanitize_url ( $value ) {
			return esc_url_raw ( $value );
		}
		
		/*
		 * Do the shortcode
		 * @since 1.0.0
		 */
		public function contact_details_shortcode ( $atts ) {
			
			$args = shortcode_atts (
				array (
					'types'		=> '', // Comma separated list of contact types to display, e.g. standard, social. Blank for all
					'fields'	=> '', // Comma separated list of contact fields to display, e.g. phone, email. Blank for all
					'format'	=> 'vertical'
				),
				$atts
			);
			
			// If a type is specified, create a new array
			if ( ! empty ( $args['types'] ) ) {
				$args['types'] = explode ( ',', $args['types'] );
			}
			// If a field is specified, create a new array
			if ( ! empty ( $args['fields'] ) ) {
				$args['fields'] = explode ( ',', $args['fields'] );
			}
			
			$return = bcd_get_contact_details ( $args );

			return $return;
			
		}
		
	}

}