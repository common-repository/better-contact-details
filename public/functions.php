<?php
/*
 * Better Contact Details public class
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*
 * Our basic functions to output contact details
 * Used by shortcodes and widgets too
 * @param $args	array
 * @since 1.0.0
 */
if ( ! function_exists ( 'bcd_get_contact_details' ) ) {
	
	function bcd_get_contact_details ( $args ) {
		
		$return = '';
		
		$groups = bcd_settings_groups();
		$settings = bcd_get_settings();
		$general = get_option ( 'settings_details_settings' );
		$labels = get_option ( 'labels_details_settings' );
		$font_awesome = ! empty ( $general['enqueue_font_awesome'] ) ? 1 : 0;
		$hide_labels = ! empty ( $general['hide_labels'] ) ? 1 : 0;
		$use_styles = ! empty ( $general['add_styles'] ) ? 1 : 0;

		// Check if types are specified
		if ( ! empty ( $args['types'] ) ) {
			
			foreach ( $args['types'] as $type ) {

				// Get all fields in $type
				if ( $type == 'standard' ) {
					$fields = $settings['standard_details']['fields'];
					$options = get_option ( $settings['standard_details']['section_id'] . '_settings' );
				} else if ( $type == 'social' )	{
					$fields = $settings['social_details']['fields'];
					$options = get_option ( $settings['social_details']['section_id'] . '_settings' );
				} else {
					$fields = array();
				}
				
				if ( ! empty ( $fields ) ) {
					
					foreach ( $fields as $field ) {
						
						if ( ! empty ( $options[$field['id']] ) ) {
							
							// Get the value
							$value = $options[$field['id']];
						
							// Insert the icon if enabled
							if ( true == $font_awesome ) {
								$icon = '<i class="fa fa-fw fa-' . $field['icon'] . '"></i>';
							} else {
								$icon = '';
							}
		
							// Pass the value through a callback to escape it
							// We use the same callback field as the Settings use to create the input field
							if ( ! empty ( $field['callback'] ) ) {
								
								if ( ! empty ( $labels[$field['id'] . '_label'] ) ) {
									$label = $labels[$field['id'] . '_label'] . '&nbsp;';
								} else if ( ! empty ( $field['label'] ) ) {
									// Fallback label
									$label = $field['label'] . '&nbsp;';
								} else {
									$label = '';
								}
								$value = call_user_func ( 'bcd_' . $field['callback'], $value, $label, $icon, $hide_labels, $type );
							}
		
							$return .= '<li>' . $value . '</li>';
						
						}
		
					}
					
				}
				
			}
			
		}
		
		// Check if fields are specified
		if ( ! empty ( $args['fields'] ) ) {
			
			foreach ( $args['fields'] as $field ) {

				// Find the settings group that the field belongs to
				if ( isset ( $groups[$field]['group'] ) ) {
					
					$type = substr ( $groups[$field]['group'], 0, strpos ( $groups[$field]['group'], '_' ) );
					
					$options = get_option ( $groups[$field]['group'] );
					
					// Get the value
					if ( ! empty ( $options[$groups[$field]['id']] ) ) {
						
						$value = $options[$groups[$field]['id']];
						
						// Insert the icon if enabled
						if ( true == $font_awesome ) {
							$icon = '<i class="fa fa-fw fa-' . $groups[$field]['icon'] . '"></i>';
						} else {
							$icon = '';
						}
						
						// Define the label
						if ( ! empty ( $labels[$groups[$field]['id'] . '_label'] ) ) {
							$label = $labels[$groups[$field]['id'] . '_label'] . '&nbsp;';
						} else if ( ! empty ( $groups[$field]['label'] ) ) {
							// Fallback label
							$label = $groups[$field]['label'] . '&nbsp;';
						} else {
							$label = '';
						}

						// Pass the value through a callback to escape it
						// We use the same callback field as the Settings use to create the input field
						if ( ! empty ( $groups[$field]['callback'] ) ) {
							$value = call_user_func ( 'bcd_' . $groups[$field]['callback'], $value, $label, $icon, $hide_labels, $type );
						}

						$return .= '<li>' . $value . '</li>';
						
					}
					
				}
				
			}
			
		}
		
		// If we've got some value to return then wrap them in an <ul>
		$classes = array ( 'bcd-contact-list' );
		$classes[] = $args['format'] . '-list';
		if ( true == $font_awesome ) {
			$classes[] = 'has-icons';
		}
		if ( ! empty ( $return ) ) {
			$return = '<ul class="' . join ( ' ', $classes ) . '">' . $return . '</ul><!-- .bcd-contact-list -->';
		}
		
		return $return;
		
	}
	
}

/*
 * Use this in your theme
 * @param $args	array
 * @since 1.0.0
 */
if ( ! function_exists ( 'bcd_contact_details' ) ) {
	function bcd_contact_details ( $args ) {
		echo bcd_get_contact_details ( $args );
	}
}
		
/*
 * These are our available settings fields
 * @since 1.0.0
 */
function bcd_get_settings() {

	$settings = array (
		
		// Each section represents a panel in the Customizer and a tab in the Settings
		'standard_details'	=>	array (
			'section_id'	=>	'standard_details',
			'section_title'	=>	__( 'Standard Details', 'better-contact-details' ),
			'priority'		=>	10,
			'fields'		=>	array (
				array (
					// These are for the Settings API
					'id'		=>	'phone',
					'title'		=>	__( 'Phone Number', 'better-contact-details' ),
					'callback'	=> 'render_text_field',
					'page'		=>	'',
					'section'	=>	'',
					'args'		=>	array(),
					// These are for the Customizer control
					'type'		=>	'text',
					'priority'	=>	10,
					'section'	=>	'contact_details',
					'label'		=>	__( 'Phone Number', 'better-contact-details' ), // Same as title above
					'choices'	=>	array(),
					'desc'		=>	'', // Optional description
					// These are just helpful
					'sanitize'	=>	'sanitize_text',
					'icon'		=>	'phone'
				),
				array (
					// These are for the Settings API
					'id'		=>	'mobile',
					'title'		=>	__( 'Mobile Number', 'better-contact-details' ),
					'callback'	=> 'render_text_field',
					'page'		=>	'',
					'section'	=>	'',
					'args'		=>	array(),
					// These are for the Customizer control
					'type'		=>	'text',
					'priority'	=>	20,
					'section'	=>	'contact_details',
					'label'		=>	__( 'Mobile Number', 'better-contact-details' ), // Same as title above
					'choices'	=>	array(),
					'desc'		=>	'', // Optional description
					'sanitize'	=>	'sanitize_text',
					'icon'		=>	'mobile'
				),
				array (
					// These are for the Settings API
					'id'		=>	'email',
					'title'		=>	__( 'Email Address', 'better-contact-details' ),
					'callback'	=> 'render_email_field',
					'page'		=>	'',
					'section'	=>	'',
					'args'		=>	array(),
					// These are for the Customizer control
					'type'		=>	'email',
					'priority'	=>	30,
					'section'	=>	'contact_details',
					'label'		=>	__( 'Email', 'better-contact-details' ), // Same as title above
					'choices'	=>	array(),
					'desc'		=>	'', // Optional description
					'sanitize'	=>	'sanitize_email', // Callback to sanitize,
					'icon'		=>	'envelope'
				),
				array (
					// These are for the Settings API
					'id'		=>	'fax',
					'title'		=>	__( 'Fax Number', 'better-contact-details' ),
					'callback'	=> 'render_text_field',
					'page'		=>	'',
					'section'	=>	'',
					'args'		=>	array(),
					// These are for the Customizer control
					'type'		=>	'email',
					'priority'	=>	35,
					'section'	=>	'contact_details',
					'label'		=>	__( 'Fax', 'better-contact-details' ), // Same as title above
					'choices'	=>	array(),
					'desc'		=>	'', // Optional description
					'sanitize'	=>	'sanitize_text', // Callback to sanitize,
					'icon'		=>	'fax'
				),
				array (
					// These are for the Settings API
					'id'		=>	'address',
					'title'		=>	__( 'Address', 'better-contact-details' ),
					'callback'	=> 'render_textarea',
					'page'		=>	'',
					'section'	=>	'',
					'args'		=>	array(),
					// These are for the Customizer control
					'type'		=>	'textarea',
					'priority'	=>	40,
					'section'	=>	'contact_details',
					'label'		=>	__( 'Address', 'better-contact-details' ), // Same as title above
					'choices'	=>	array(),
					'desc'		=>	'', // Optional description
					'sanitize'	=>	'sanitize_textarea', // Callback to sanitize,
					'icon'		=>	'map-marker'
				),
			),
		),
		'social_details'	=>	array (
			'section_id'	=>	'social_details',
			'section_title'	=>	__( 'Social Details', 'better-contact-details' ),
			'priority'		=>	20,
			'fields'		=>	array (
				array (
					// These are for the Settings API
					'id'		=>	'twitter',
					'title'		=>	__( 'Twitter', 'better-contact-details' ),
					'callback'	=> 'render_url_field',
					'page'		=>	'',
					'section'	=>	'',
					'args'		=>	array(),
					// These are for the Customizer control
					'type'		=>	'url',
					'priority'	=>	10,
					'section'	=>	'contact_details',
					'label'		=>	__( 'Twitter', 'better-contact-details' ), // Same as title above
					'choices'	=>	array(),
					'desc'		=>	'', // Optional description
					'sanitize'	=>	'sanitize_url',
					'icon'		=>	'twitter'
				),
				array (
					// These are for the Settings API
					'id'		=>	'facebook',
					'title'		=>	__( 'Facebook', 'better-contact-details' ),
					'callback'	=> 'render_url_field',
					'page'		=>	'',
					'section'	=>	'',
					'args'		=>	array(),
					// These are for the Customizer control
					'type'		=>	'url',
					'priority'	=>	20,
					'section'	=>	'contact_details',
					'label'		=>	__( 'Facebook', 'better-contact-details' ), // Same as title above
					'choices'	=>	array(),
					'desc'		=>	'', // Optional description
					'sanitize'	=>	'sanitize_url',
					'icon'		=>	'facebook'
				),
				array (
					// These are for the Settings API
					'id'		=>	'instagram',
					'title'		=>	__( 'Instagram', 'better-contact-details' ),
					'callback'	=> 'render_url_field',
					'page'		=>	'',
					'section'	=>	'',
					'args'		=>	array(),
					// These are for the Customizer control
					'type'		=>	'url',
					'priority'	=>	30,
					'section'	=>	'contact_details',
					'label'		=>	__( 'Instagram', 'better-contact-details' ), // Same as title above
					'choices'	=>	array(),
					'desc'		=>	'', // Optional description
					'sanitize'	=>	'sanitize_url',
					'icon'		=>	'instagram'
				),
				array (
					// These are for the Settings API
					'id'		=>	'pinterest',
					'title'		=>	__( 'Pinterest', 'better-contact-details' ),
					'callback'	=> 'render_url_field',
					'page'		=>	'',
					'section'	=>	'',
					'args'		=>	array(),
					// These are for the Customizer control
					'type'		=>	'url',
					'priority'	=>	40,
					'section'	=>	'contact_details',
					'label'		=>	__( 'Pinterest', 'better-contact-details' ), // Same as title above
					'choices'	=>	array(),
					'desc'		=>	'', // Optional description
					'sanitize'	=>	'sanitize_url',
					'icon'		=>	'pinterest'
				),
				array (
					// These are for the Settings API
					'id'		=>	'google_plus',
					'title'		=>	__( 'Google Plus', 'better-contact-details' ),
					'callback'	=> 'render_url_field',
					'page'		=>	'',
					'section'	=>	'',
					'args'		=>	array(),
					// These are for the Customizer control
					'type'		=>	'url',
					'priority'	=>	50,
					'section'	=>	'contact_details',
					'label'		=>	__( 'Google Plus', 'better-contact-details' ), // Same as title above
					'choices'	=>	array(),
					'desc'		=>	'', // Optional description
					'sanitize'	=>	'sanitize_url',
					'icon'		=>	'google-plus'
				),
				array (
					// These are for the Settings API
					'id'		=>	'linkedin',
					'title'		=>	__( 'LinkedIn', 'better-contact-details' ),
					'callback'	=> 'render_url_field',
					'page'		=>	'',
					'section'	=>	'',
					'args'		=>	array(),
					// These are for the Customizer control
					'type'		=>	'url',
					'priority'	=>	60,
					'section'	=>	'contact_details',
					'label'		=>	__( 'LinkedIn', 'better-contact-details' ), // Same as title above
					'choices'	=>	array(),
					'desc'		=>	'', // Optional description
					'sanitize'	=>	'sanitize_url',
					'icon'		=>	'linkedin'
				),
				array (
					// These are for the Settings API
					'id'		=>	'vimeo',
					'title'		=>	__( 'Vimeo', 'better-contact-details' ),
					'callback'	=> 'render_url_field',
					'page'		=>	'',
					'section'	=>	'',
					'args'		=>	array(),
					// These are for the Customizer control
					'type'		=>	'url',
					'priority'	=>	70,
					'section'	=>	'contact_details',
					'label'		=>	__( 'Vimeo', 'better-contact-details' ), // Same as title above
					'choices'	=>	array(),
					'desc'		=>	'', // Optional description
					'sanitize'	=>	'sanitize_url',
					'icon'		=>	'vimeo'
				),
				array (
					// These are for the Settings API
					'id'		=>	'youtube',
					'title'		=>	__( 'YouTube', 'better-contact-details' ),
					'callback'	=> 'render_url_field',
					'page'		=>	'',
					'section'	=>	'',
					'args'		=>	array(),
					// These are for the Customizer control
					'type'		=>	'url',
					'priority'	=>	80,
					'section'	=>	'contact_details',
					'label'		=>	__( 'YouTube', 'better-contact-details' ), // Same as title above
					'choices'	=>	array(),
					'desc'		=>	'', // Optional description
					'sanitize'	=>	'sanitize_url',
					'icon'		=>	'youtube'
				),
			),
		),
		'settings_details'	=>	array (
			'section_id'	=>	'settings_details',
			'section_title'	=>	__( 'Settings', 'better-contact-details' ),
			'priority'		=>	30,
			'fields'		=>	array (
				array (
					// These are for the Settings API
					'id'		=>	'enqueue_font_awesome',
					'title'		=>	__( 'Use Icons?', 'better-contact-details' ),
					'callback'	=> 'render_checkbox_field',
					'page'		=>	'',
					'section'	=>	'',
					// These are for the Customizer control
					'type'		=>	'checkbox',
					'priority'	=>	10,
					'section'	=>	'contact_details',
					'label'		=>	__( 'Use Icons?', 'better-contact-details' ), // Same as title above
					'choices'	=>	array(),
					'desc'		=>	__( 'Checking this will enqueue the Font Awesome icon set', 'better-contact-details' ), // Optional description
					'sanitize'	=>	'',
					'icon'		=>	'',
					'default'	=>	1
				),
				array (
					// These are for the Settings API
					'id'		=>	'hide_labels',
					'title'		=>	__( 'Hide Labels for Social?', 'better-contact-details' ),
					'callback'	=> 'render_checkbox_field',
					'page'		=>	'',
					'section'	=>	'',
					// These are for the Customizer control
					'type'		=>	'checkbox',
					'priority'	=>	20,
					'section'	=>	'contact_details',
					'label'		=>	__( 'Hide Labels for Social?', 'better-contact-details' ), // Same as title above
					'choices'	=>	array(),
					'desc'		=>	__( 'Select this to get a list of clickable icons (no text) for your social media links', 'better-contact-details' ), // Optional description
					'sanitize'	=>	'',
					'icon'		=>	'',
					'default'	=>	1
				),
				array (
					// These are for the Settings API
					'id'		=>	'add_styles',
					'title'		=>	__( 'Add List Styles?', 'better-contact-details' ),
					'callback'	=> 'render_checkbox_field',
					'page'		=>	'',
					'section'	=>	'',
					// These are for the Customizer control
					'type'		=>	'checkbox',
					'priority'	=>	30,
					'section'	=>	'contact_details',
					'label'		=>	__( 'Add List Styles?', 'better-contact-details' ), // Same as title above
					'choices'	=>	array(),
					'desc'		=>	'',
					'sanitize'	=>	'',
					'icon'		=>	'',
					'default'	=>	1
				),
			),
		),
		'labels_details'	=>	array (
			'section_id'	=>	'labels_details',
			'section_title'	=>	__( 'Labels', 'better-contact-details' ),
			'priority'		=>	40,
			'fields'		=>	array (
				array (
					// These are for the Settings API
					'id'		=>	'phone_label',
					'title'		=>	__( 'Phone Number Label', 'better-contact-details' ),
					'callback'	=> 'render_text_field',
					'page'		=>	'',
					'section'	=>	'',
					'args'		=>	array(),
					// These are for the Customizer control
					'type'		=>	'text',
					'priority'	=>	5,
					'section'	=>	'contact_details',
					'label'		=>	__( 'Phone Number Label', 'better-contact-details' ), // Same as title above
					'choices'	=>	array(),
					'desc'		=>	'', // Optional description
					// These are just helpful
					'default'	=>	__( 'Phone:', 'better-contact-details' ),
					'sanitize'	=>	'sanitize_text',
					'icon'		=>	''
				),
				array (
					// These are for the Settings API
					'id'		=>	'mobile_label',
					'title'		=>	__( 'Mobile Number Label', 'better-contact-details' ),
					'callback'	=> 'render_text_field',
					'page'		=>	'',
					'section'	=>	'',
					'args'		=>	array(),
					// These are for the Customizer control
					'type'		=>	'text',
					'priority'	=>	15,
					'section'	=>	'contact_details',
					'label'		=>	__( 'Mobile Number Label', 'better-contact-details' ), // Same as title above
					'choices'	=>	array(),
					'desc'		=>	'', // Optional description
					// These are just helpful
					'default'	=>	__( 'Mobile:', 'better-contact-details' ),
					'sanitize'	=>	'sanitize_text',
					'icon'		=>	''
				),
				array (
					// These are for the Settings API
					'id'		=>	'email_label',
					'title'		=>	__( 'Email Label', 'better-contact-details' ),
					'callback'	=> 'render_text_field',
					'page'		=>	'',
					'section'	=>	'',
					'args'		=>	array(),
					// These are for the Customizer control
					'type'		=>	'text',
					'priority'	=>	25,
					'section'	=>	'contact_details',
					'label'		=>	__( 'Email label', 'better-contact-details' ), // Same as title above
					'choices'	=>	array(),
					'desc'		=>	'', // Optional description
					// These are just helpful
					'default'	=>	__( 'Email:', 'better-contact-details' ),
					'sanitize'	=>	'sanitize_text',
					'icon'		=>	''
				),
				array (
					// These are for the Settings API
					'id'		=>	'fax_label',
					'title'		=>	__( 'Fax Label', 'better-contact-details' ),
					'callback'	=> 'render_text_field',
					'page'		=>	'',
					'section'	=>	'',
					'args'		=>	array(),
					// These are for the Customizer control
					'type'		=>	'text',
					'priority'	=>	30,
					'section'	=>	'contact_details',
					'label'		=>	__( 'Fax label', 'better-contact-details' ), // Same as title above
					'choices'	=>	array(),
					'desc'		=>	'', // Optional description
					// These are just helpful
					'default'	=>	__( 'Fax:', 'better-contact-details' ),
					'sanitize'	=>	'sanitize_text',
					'icon'		=>	''
				),
				array (
					// These are for the Settings API
					'id'		=>	'address_label',
					'title'		=>	__( 'Address Label', 'better-contact-details' ),
					'callback'	=> 'render_text_field',
					'page'		=>	'',
					'section'	=>	'',
					'args'		=>	array(),
					// These are for the Customizer control
					'type'		=>	'text',
					'priority'	=>	35,
					'section'	=>	'contact_details',
					'label'		=>	__( 'Address label', 'better-contact-details' ), // Same as title above
					'choices'	=>	array(),
					'desc'		=>	'', // Optional description
					// These are just helpful
					'default'	=>	__( 'Address:', 'better-contact-details' ),
					'sanitize'	=>	'sanitize_text',
					'icon'		=>	''
				),
				array (
					// These are for the Settings API
					'id'		=>	'twitter_label',
					'title'		=>	__( 'Twitter Label', 'better-contact-details' ),
					'callback'	=> 'render_text_field',
					'page'		=>	'',
					'section'	=>	'',
					'args'		=>	array(),
					// These are for the Customizer control
					'type'		=>	'text',
					'priority'	=>	205,
					'section'	=>	'contact_details',
					'label'		=>	__( 'Twitter Label', 'better-contact-details' ), // Same as title above
					'choices'	=>	array(),
					'desc'		=>	'', // Optional description
					// These are just helpful
					'default'	=>	__( 'Twitter', 'better-contact-details' ),
					'sanitize'	=>	'sanitize_text',
					'icon'		=>	''
				),
				array (
					// These are for the Settings API
					'id'		=>	'facebook_label',
					'title'		=>	__( 'Facebook Label', 'better-contact-details' ),
					'callback'	=> 'render_text_field',
					'page'		=>	'',
					'section'	=>	'',
					'args'		=>	array(),
					// These are for the Customizer control
					'type'		=>	'text',
					'priority'	=>	215,
					'section'	=>	'contact_details',
					'label'		=>	__( 'Facebook Label', 'better-contact-details' ), // Same as title above
					'choices'	=>	array(),
					'desc'		=>	'', // Optional description
					// These are just helpful
					'default'	=>	__( 'Facebook', 'better-contact-details' ),
					'sanitize'	=>	'sanitize_text',
					'icon'		=>	''
				),
				array (
					// These are for the Settings API
					'id'		=>	'instagram_label',
					'title'		=>	__( 'Instagram Label', 'better-contact-details' ),
					'callback'	=> 'render_text_field',
					'page'		=>	'',
					'section'	=>	'',
					'args'		=>	array(),
					// These are for the Customizer control
					'type'		=>	'text',
					'priority'	=>	225,
					'section'	=>	'contact_details',
					'label'		=>	__( 'Instagram Label', 'better-contact-details' ), // Same as title above
					'choices'	=>	array(),
					'desc'		=>	'', // Optional description
					// These are just helpful
					'default'	=>	__( 'Instagram', 'better-contact-details' ),
					'sanitize'	=>	'sanitize_text',
					'icon'		=>	''
				),
				array (
					// These are for the Settings API
					'id'		=>	'pinterest_label',
					'title'		=>	__( 'Pinterest Label', 'better-contact-details' ),
					'callback'	=> 'render_text_field',
					'page'		=>	'',
					'section'	=>	'',
					'args'		=>	array(),
					// These are for the Customizer control
					'type'		=>	'text',
					'priority'	=>	235,
					'section'	=>	'contact_details',
					'label'		=>	__( 'Pinterest Label', 'better-contact-details' ), // Same as title above
					'choices'	=>	array(),
					'desc'		=>	'', // Optional description
					// These are just helpful
					'default'	=>	__( 'Pinterest', 'better-contact-details' ),
					'sanitize'	=>	'sanitize_text',
					'icon'		=>	''
				),
				array (
					// These are for the Settings API
					'id'		=>	'google_plus_label',
					'title'		=>	__( 'Google Plus Label', 'better-contact-details' ),
					'callback'	=> 'render_text_field',
					'page'		=>	'',
					'section'	=>	'',
					'args'		=>	array(),
					// These are for the Customizer control
					'type'		=>	'text',
					'priority'	=>	245,
					'section'	=>	'contact_details',
					'label'		=>	__( 'Google Plus Label', 'better-contact-details' ), // Same as title above
					'choices'	=>	array(),
					'desc'		=>	'', // Optional description
					// These are just helpful
					'default'	=>	__( 'Google Plus', 'better-contact-details' ),
					'sanitize'	=>	'sanitize_text',
					'icon'		=>	''
				),
				array (
					// These are for the Settings API
					'id'		=>	'linkedin_label',
					'title'		=>	__( 'LinkedIn Label', 'better-contact-details' ),
					'callback'	=> 'render_text_field',
					'page'		=>	'',
					'section'	=>	'',
					'args'		=>	array(),
					// These are for the Customizer control
					'type'		=>	'text',
					'priority'	=>	255,
					'section'	=>	'contact_details',
					'label'		=>	__( 'LinkedIn Label', 'better-contact-details' ), // Same as title above
					'choices'	=>	array(),
					'desc'		=>	'', // Optional description
					// These are just helpful
					'default'	=>	__( 'LinkedIn', 'better-contact-details' ),
					'sanitize'	=>	'sanitize_text',
					'icon'		=>	''
				),
				array (
					// These are for the Settings API
					'id'		=>	'vimeo_label',
					'title'		=>	__( 'Vimeo Label', 'better-contact-details' ),
					'callback'	=> 'render_text_field',
					'page'		=>	'',
					'section'	=>	'',
					'args'		=>	array(),
					// These are for the Customizer control
					'type'		=>	'text',
					'priority'	=>	265,
					'section'	=>	'contact_details',
					'label'		=>	__( 'Vimeo Label', 'better-contact-details' ), // Same as title above
					'choices'	=>	array(),
					'desc'		=>	'', // Optional description
					// These are just helpful
					'default'	=>	__( 'Vimeo', 'better-contact-details' ),
					'sanitize'	=>	'sanitize_text',
					'icon'		=>	''
				),
				array (
					// These are for the Settings API
					'id'		=>	'youtube_label',
					'title'		=>	__( 'YouTube Label', 'better-contact-details' ),
					'callback'	=> 'render_text_field',
					'page'		=>	'',
					'section'	=>	'',
					'args'		=>	array(),
					// These are for the Customizer control
					'type'		=>	'text',
					'priority'	=>	275,
					'section'	=>	'contact_details',
					'label'		=>	__( 'YouTube Label', 'better-contact-details' ), // Same as title above
					'choices'	=>	array(),
					'desc'		=>	'', // Optional description
					// These are just helpful
					'default'	=>	__( 'YouTube', 'better-contact-details' ),
					'sanitize'	=>	'sanitize_text',
					'icon'		=>	''
				),
			),
		),
				
	);
	
	return apply_filters ( 'bcd_settings', $settings );
		
}

/*
 * You can filter the settings to add your own fields
 * Follow the example below
 * @since 1.0.0
 */
function bcd_example_settings_filter ( $settings ) {
	// Example Social field
	$settings['social_details']['fields'][] = array (				// Add your field to social_details or standard_details
		// These are for the Settings API
		'id'		=>	'github',									// Unique ID
		'title'		=>	__( 'GitHub', 'better-contact-details' ),	// What to label your field
		'callback'	=> 'render_url_field',							// Should be render_url_field for social_details; otherwise render_text_field
		'page'		=>	'',
		'section'	=>	'',
		'args'		=>	array(),
		// These are for the Customizer control
		'type'		=>	'url',										// Should be url for social_details; otherwise text
		'priority'	=>	180,
		'section'	=>	'contact_details',
		'label'		=>	__( 'GitHub', 'better-contact-details' ),	// Same as title above
		'choices'	=>	array(),
		'desc'		=>	'', // Optional description
		'sanitize'	=>	'sanitize_url',								// Sanitization callback - either sanitize_url or sanitize_text
		'icon'		=>	'github'									// Font Awesome icon ID
	);
	// Example Labels field
	$settings['labels_details']['fields'][] = array (				// Add your field to labels_details
		// These are for the Settings API
		'id'		=>	'github_label',								// Unique ID - for labels needs to correspond to setting ID by appending _label
		'title'		=>	__( 'GitHub Label', 'better-contact-details' ),	// What to label your field
		'callback'	=> 'render_text_field',							// Should be render_url_field for social_details; otherwise render_text_field
		'page'		=>	'',
		'section'	=>	'',
		'args'		=>	array(),
		// These are for the Customizer control
		'type'		=>	'text',										// Should be url for social_details; otherwise text
		'priority'	=>	380,
		'section'	=>	'contact_details',
		'label'		=>	__( 'GitHub Label', 'better-contact-details' ),	// Same as title above
		'choices'	=>	array(),
		'desc'		=>	'', // Optional description
		'sanitize'	=>	'sanitize_text',							// Sanitization callback - either sanitize_url or sanitize_text
		'icon'		=>	'',											// Doesn't apply within label_details section
		'default'	=>	__( 'GitHub', 'better-contact-details' )	// A default value for first time plugin is activated
			
	);
	return $settings;
}
add_filter ( 'bcd_settings', 'bcd_example_settings_filter' );
		
/*
 * Reorganise the settings array so that we can find which fields belong to which settings groups
 * Save in a transient so we don't need to do this every time
 * @todo reset the transient on plugin upgrade in case any fields are added or removed
 * @since 1.0.0
 */
function bcd_settings_groups() {
	
	if ( ! empty ( $_GET['delete'] ) && 'transient' == $_GET['delete'] ) {
		delete_transient ( 'bcd_groups' );
	}
	
	// Check if the transient exists
	$transient = get_transient ( 'bcd_groups' );
	
	// If it doesn't exist then do the thing
	if ( $transient === false ) {
		
		$settings = bcd_get_settings();
		$options = array();
	
		if ( ! empty ( $settings ) ) {
			foreach ( $settings as $setting ) {
				foreach ( $setting['fields'] as $field ) {
					$options[$field['id']] = array (
						'id'		=>	$field['id'],
						'group'		=>	$setting['section_id'] . '_settings',
						'callback'	=>	$field['callback'],
						'label'		=>	$field['label'],
						'icon'		=>	$field['icon']
					);
				}
			}
		}
		
		// Give it an expiration of 30 days, doesn't hurt
		set_transient ( 'bcd_groups', $options, 30 * DAY_IN_SECONDS );
		
	} else {
		
		$options = $transient;
		
	}
	
	return $options;
	
}

/*
 * Function to escape the field values
 * @since 1.0.0
 */
function bcd_render_text_field ( $value, $label, $icon, $hide_labels, $type ) {

	// Add the label
	if ( ( 'social' == $type && true == $hide_labels ) && ! empty ( $icon ) ) {
		$value = '&nbsp;';
	} else if ( empty ( $icon ) ) {
		$value = esc_html ( $label . $value );
	} else {
		$value = $icon . esc_html ( $value );
	}
	
	$value = $value;
	
	return $value;	
}

function bcd_render_url_field ( $value, $label, $icon, $hide_labels, $type ) {

	// Hide the label if selected for social links
	if ( ( 'social' == $type && true == $hide_labels ) && ! empty ( $icon ) ) {
		$label = '&nbsp;';
	} else if ( 'standard' == $type && ! empty ( $icon ) ) {
		$label = str_replace ( '&nbsp;', '', $icon );
	}
	
	$value = '<a href="' . esc_url ( $value ) . '">' . $icon . esc_html ( $label ) . '</a>';
	
	return $value;	
}

function bcd_render_email_field ( $value, $label, $icon, $hide_labels, $type ) {
	
	// Add the label
	if ( ( 'social' == $type && true == $hide_labels ) && ! empty ( $icon ) ) {
		$label = $icon;
	} else if ( 'standard' == $type && ! empty ( $icon ) ) {
		$label = str_replace ( '&nbsp;', '', $icon );
	}
	
	if ( is_email ( $value ) ) {
		return $label . '<a href="mailto:' . $value . '">' . $value . '</a>';
	} else {
		return '';
	}
	
}

function bcd_render_textarea ( $value, $label, $icon, $hide_labels, $type ) {

	// Add the label
	if ( ( 'social' == $type && true == $hide_labels ) && ! empty ( $icon ) ) {
		$label = '';
	} else if ( 'standard' == $type && ! empty ( $icon ) ) {
		$icon = str_replace ( '&nbsp;', '', $icon );
	} else {
		$value = $label . '&nbsp;' . $value;
	}
	
	$value = $icon . wp_kses ( nl2br ( $value ), array ( 'br' => array() ) );
	
	return $value;	
}