<?php
/*
 * Better Contact Details admin class
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Plugin public class
 **/
if ( ! class_exists( 'BCD_Admin' ) ) {

	class BCD_Admin {
		
		private $settings = array(); 
		
		public function __construct() {
			$this -> settings = bcd_get_settings();
		}
		
		/*
		 * Initialize the class and start calling our hooks and filters
		 * @since 1.0.0
		 */
		public function init() {
			add_action ( 'admin_menu', array ( $this, 'add_admin_menu' ) );
			add_action ( 'admin_init', array ( $this, 'register_settings' ) );
			add_action ( 'admin_enqueue_scripts', array ( $this, 'enqueue_scripts' ) );
		}
		
		
		/*
		 * Add stylesheets for admin
		 * @since 1.0.0
		 */
		public function enqueue_scripts() {
			if ( ! empty ( $_GET['page'] ) && $_GET['page'] == 'contact_details' ) {
				wp_enqueue_style ( 'bcd-admin-style', BCD_PLUGIN_URL . 'assets/css/admin-style.css', array(), '1.0.0' );
			}
		}
		
		/*
		 * Add the menu page
		 * @since 1.0.0
		 */
		public function add_admin_menu() {
			add_options_page ( 
				__('Contact Details', 'better-contact-details'),
				__('Contact Details', 'better-contact-details'),
				'manage_options',
				'contact_details',
				array ( $this, 'options_page' )
			);
		}
		
		/*
		 * Register our settings
		 * @since 1.0.0
		 */
		public function register_settings() {
			
			foreach ( $this->settings as $setting ) {
				
				register_setting ( $setting['section_id'], $setting['section_id'] . '_settings', array ( $this, 'sanitize_input' ) );
			
				add_settings_section (
					$setting['section_id'] . '_section', 
					$setting['section_title'], 
					'', 
					$setting['section_id']
				);
			
				foreach ( $setting['fields'] as $field ) {
					$field['settings_id'] = $setting['section_id'] . '_settings'; // Add this here so we can pass it to the callback
					add_settings_field ( 
						$field['id'], 
						$field['title'], 
						array ( $this, $field['callback'] ),
						$setting['section_id'], 
						$setting['section_id'] . '_section',
						$field // Pass the element data to the callback
					);
					
				}

			}
			
			// Apply defaults first time around
			$defaults_applied = get_option ( 'bcd_defaults_applied' );
			if ( false === $defaults_applied ) {
				$this -> apply_defaults();
				update_option ( 'bcd_defaults_applied', 1 );
			}

		}
		
		
		/* 
		 * Sanitize the values
		 * @since 1.0.0
		 */
		public function sanitize_input ( $input ) {

			$output = array();
			
			if ( $input ) {
			
				foreach ( $input as $key => $value ) {
			
					if ( isset ( $input[$key] ) ) {
					
						$output[$key] = strip_tags ( stripslashes ( $input[$key] ) );
					
					}
				
				}
			
			}
			
			return apply_filters ( 'bcd_input_values', $output, $input );
			
		}
		/* 
		 * Do the default settings
		 * @since 1.0.0
		 */
		public function apply_defaults() {
	
			if ( ! empty ( $this -> settings ) ) {
				
				foreach ( $this -> settings as $setting ) {
					
					// Reset the defaults for each group
					$defaults = array();
					
					foreach ( $setting['fields'] as $field ) {
						if ( isset ( $field['default'] ) ) {
							$defaults[$field['id']] = $field['default'];
						}
					}
					
					// Apply the defaults
					update_option ( $setting['section_id'] . '_settings', $defaults );
					
				}
				
			}
			
		}
		
		/*
		 * Render a text field
		 * @since 1.0.0
		 */
		public function render_text_field ( $args ) { 
			$options = get_option ( $args['settings_id'] ); 
			$name = $args['settings_id'] . '[' . $args['id'] . ']';
			$value = '';
			if ( isset ( $options[$args['id']] ) ) {
				$value = $options[$args['id']];
			} ?>
			<input type="text" name="<?php echo esc_attr ( $name ); ?>" value="<?php echo esc_attr ( $value ); ?>">
			<p class="description"><?php echo esc_html ( $args['desc'] ); ?></p>
		<?php
		}
		
		/*
		 * Render a text field
		 * @since 1.0.0
		 */
		public function render_textarea ( $args ) { 
			$options = get_option ( $args['settings_id'] ); 
			$name = $args['settings_id'] . '[' . $args['id'] . ']';
			$value = '';
			if ( isset ( $options[$args['id']] ) ) {
				$value = $options[$args['id']];
			} ?>
			<textarea name="<?php echo esc_attr ( $name ); ?>"><?php echo esc_html ( $value ); ?></textarea>
			<p class="description"><?php echo esc_html ( $args['desc'] ); ?></p>
		<?php
		}
		
		/*
		 * Render a URL field
		 * @since 1.0.0
		 */
		public function render_url_field ( $args ) { 
			$options = get_option ( $args['settings_id'] ); 
			$name = $args['settings_id'] . '[' . $args['id'] . ']';
			$value = '';
			if ( isset ( $options[$args['id']] ) ) {
				$value = $options[$args['id']];
			} ?>
			<input type="url" name="<?php echo esc_attr ( $name ); ?>" value="<?php echo esc_url ( $value ); ?>">
			<p class="description"><?php echo esc_html ( $args['desc'] ); ?></p>
		<?php
		}
		
		
		/*
		 * Render an email field
		 * @since 1.0.0
		 */
		public function render_email_field ( $args ) { 
			$options = get_option ( $args['settings_id'] ); 
			$name = $args['settings_id'] . '[' . $args['id'] . ']';
			$value = '';
			if ( isset ( $options[$args['id']] ) && is_email ( $options[$args['id']] ) ) {
				$value = $options[$args['id']];
			} ?>
			<input type="email" name="<?php echo esc_attr ( $name ); ?>" value="<?php echo esc_attr ( $value ); ?>">
			<p class="description"><?php echo esc_html ( $args['desc'] ); ?></p>
		<?php
		}
		
		/*
		 * Render a checkbox field
		 * @since 1.0.0
		 */
		public function render_checkbox_field ( $args ) { 
			$options = get_option ( $args['settings_id'] ); 
			$name = $args['settings_id'] . '[' . $args['id'] . ']';
			$value = '';
			if ( isset ( $options[$args['id']] ) ) {
				$value = $options[$args['id']];
			} ?>
			<input type="checkbox" name="<?php echo esc_attr ( $name ); ?>" <?php checked ( ! empty ( $value ), 1 ); ?> value="1">
			<p class="description"><?php echo esc_html ( $args['desc'] ); ?></p>
		<?php
		}
		
		/*
		 * Output the options page
		 * @since 1.0.0
		 */
		public function options_page() {
			
			$current = isset ( $_GET['tab'] ) ? $_GET['tab'] : 'standard';
			$title =  __( 'Contact Details', 'better-contact-widgets' );
			$tabs = array (
				'standard'		=>	__( 'Standard', 'better-contact-widgets' ),
				'social'		=>	__( 'Social', 'better-contact-widgets' ),
				'settings'		=>	__( 'Settings', 'better-contact-widgets' ),
				'labels'		=>	__( 'Labels', 'better-contact-widgets' )
			);?>
			
			<div class="wrap">
				<h1><?php echo $title; ?></h1>
				<div class="ctdb-outer-wrap">
					<div class="ctdb-inner-wrap">
						<h2 class="nav-tab-wrapper">
							<?php foreach( $tabs as $tab => $name ) {
								$class = ( $tab == $current ) ? ' nav-tab-active' : '';
								echo "<a class='nav-tab$class' href='?page=contact_details&tab=$tab'>$name</a>";
							} ?>
						</h2>
						<form action='options.php' method='post'>
							<?php
							settings_fields( strtolower ( $current ) . '_details' );
							do_settings_sections( strtolower ( $current ) . '_details' );
							submit_button();
							?>
						</form>
					</div><!-- .ctdb-inner-wrap -->
					<div class="ctdb-banners">
						<div class="ctdb-banner">							<a href="https://catapultthemes.com/downloads/super-hero-slider-pro/?utm_source=plugin_ad&utm_medium=wp_plugin&utm_content=bcd&utm_campaign=campaign">								<img src="<?php echo BCD_PLUGIN_URL . 'assets/images/superhero-ad.png'; ?>" alt="" >							</a>						</div>						<div class="ctdb-banner">
							<a href="https://catapultthemes.com/downloads/category/themes/?utm_source=plugin_ad&utm_medium=wp_plugin&utm_content=bcd&utm_campaign=campaign">
								<img src="<?php echo BCD_PLUGIN_URL . 'assets/images/themes-ad.png'; ?>" alt="" >
							</a>
						</div>						<div class="ctdb-banner">
							<a href="https://sellastic.com/?utm_source=plugin_ad&utm_medium=wp_plugin&utm_content=bcd&utm_campaign=sellastic">
								<img src="<?php echo BCD_PLUGIN_URL . 'assets/images/sellastic-ad.jpg'; ?>" alt="" >
							</a>
						</div>						
					</div>
				</div><!-- .ctdb-outer-wrap -->
			</div><!-- .wrap -->
			<?php
		}
		
	}

}