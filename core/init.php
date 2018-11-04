<?php
	/**
	* init block loader
	*/
	class BlocksLoaderInit
	{
		static $BlocksLoader = NULL;
		static $args = NULL;

		function __construct()
		{
			// require_once dirname(__FILE__) . '/config.php';
			// load core files & libraries
			add_action( 'init', array($this, 'coreLoader') );
			// load admin files & libraries
			add_action( 'init', array($this, 'adminFilesLoader'), 1 );
			// load front files & libraries
			add_action( 'init', array($this, 'frontFilesLoader') );
			// setup block
			add_action( 'init', array($this, 'block_setup') );
			// generate block
			add_action( 'init', array($this, 'BlocksLoader') );
			// generate shortcode
			add_action( 'init', array($this, 'shortcode_generator') );
			// load required plugins
			add_action( 'tgmpa_register', array($this, 'tgm_required_plugins'), 2 );
			// hook wp head
			add_action( 'wp_head', array($this, 'hook_wp_head') );
			// hook wp footer
			add_action( 'wp_footer', array($this, 'hook_wp_footer') );
			// hook cmb2 submit button
			add_action( 'cmb2_render_button', array($this, 'cmb2_render_callback_for_button'), 10, 5 );
			// load ajax
			add_action( 'wp_ajax_basetheme_import_options', array($this, 'basetheme_import_options') );
    		add_action( 'wp_ajax_nopriv_basetheme_import_options', array($this, 'basetheme_import_options') );

		}

		// setup block
		static function block_setup()
		{
			self::$args = array();
			$blocks = get_posts(array('post_type' => 'blocks', 'posts_per_page' => -1, 'order' => 'ASC', 'orderby' => 'ID'));
	        foreach ($blocks as $block) {
	        	$block_id = strtolower(str_replace('-', '_', $block->post_name));
	        	self::$args[$block_id] = get_option( Config::$prefix.$block_id );
			}
			return self::$args;
		}

		// block loader
		static function BlocksLoader() {
			if ( is_null(self::$BlocksLoader) && !is_admin()) {
				return self::$BlocksLoader = new BlocksLoader(self::$args);
			}
		}

		// load core files & libraries		
		static function coreLoader() {
			// require_once dirname(__FILE__) . '/config.php';
			require_once dirname(__FILE__) . '/libraries/CMB2/init.php';
		}

		// load admin files & libraries
		static function adminFilesLoader() {
			if (!is_admin()) return;
			require_once dirname(__FILE__) . '/libraries/tgm-plugin-activation/class-tgm-plugin-activation.php';
			require_once dirname(__FILE__) . '/libraries/CMB2-conditionals/cmb2-conditionals.php';
			require_once dirname(__FILE__) . '/libraries/CMB2-repeatable-fields/cmb2-groups.php';
			require_once dirname(__FILE__) . '/generator/blocks.php';
			require_once dirname(__FILE__) . '/theme-options/theme_options.php';
			// enqueue styles and scripts
			if (self::is_basetheme_page()) {
				wp_enqueue_style( 'basetheme-style-css', Config::core_assets_css('bootstrap.min'), array(), microtime() );
				wp_enqueue_style( 'basetheme-bootstrap-css', Config::core_assets_css('basetheme'), array(), microtime() );
				wp_enqueue_script( 'basetheme-bootstrap-js', Config::core_assets_js('bootstrap.min') , array('jquery'), microtime(), true );
				wp_enqueue_script( 'basetheme-scripts-js', Config::core_assets_js('basetheme') , array('jquery'), microtime(), true );
				wp_localize_script('basetheme-scripts-js', 'BASETHEME_OBJECT', 
					array( 'ajaxurl' => admin_url( 'admin-ajax.php'), 'security' => wp_create_nonce( "uploader-ajax" )));
			}
		}

		// load front files & libraries
		static function frontFilesLoader() {
			if (is_admin()) return;
			require_once dirname(__FILE__) . '/generator/BlocksLoader.php';
			require_once dirname(__FILE__) . '/generator/ShortcodeGenerator.php';
			require_once dirname(__FILE__) . '/newsletter/newsletter.php';
			if (Config::$newsletter_options['basetheme_show_newsletter']) {
				wp_enqueue_script( 'basetheme-newsletter-js', Config::core_assets_js('newsletter') , array('jquery'), microtime(), true );
				wp_localize_script('basetheme-newsletter-js', 'NEWSLETTER_OBJECT', 
					array( 'ajaxurl' => admin_url( 'admin-ajax.php'), 'security' => wp_create_nonce( "uploader-ajax" )));
			}
		}

		// options import
		static function basetheme_import_options()
		{
			$data = [];
			$data['status'] = false;
	        if(check_ajax_referer( 'uploader-ajax', 'security' ) ) {
	            $import_options = filter_var($_POST['param']['import_options']);
            	$import_options = unserialize(base64_decode($import_options));
            	foreach ($import_options as $key => $option) {
            		update_option( $key, unserialize($option) );
            	}
            	$data['status'] = true;
	            if ($data) {
	                die(json_encode(array("error" => 0, 'message' => $data)));
	            }else{
	                die(json_encode(array("error" => 1, 'message' => "Array Empty")));
	            } 
	        }else{
	            die(json_encode(array("error" => 1, 'message' => ' No permission !!')));
	        }
	        die();
		}

		// WP head hook
		public function hook_wp_head()
		{
			print !empty(Config::$general_options['basetheme_hook_wp_head']) ? Config::$general_options['basetheme_hook_wp_head'] : '';
		}

		// WP footer hook
		public function hook_wp_footer()
		{
			print !empty(Config::$general_options['basetheme_hook_wp_footer']) ? Config::$general_options['basetheme_hook_wp_footer'] : '';
		}

		/**
	     * Setup the Shortcode generator
	     *
	     */

	    public function shortcode_generator()
	    {   
	    	if (class_exists('ShortCodeGenerator')) {
		        $ShortCodeGenerator = new ShortCodeGenerator( self::$args );
		        return $ShortCodeGenerator->generate();
			}
	    }

	    // required plugins
		/**
		 * Register the required plugins for this theme.
		 *
		 * In this example, we register two plugins - one included with the TGMPA library
		 * and one from the .org repo.
		 *
		 * The variable passed to tgmpa_register_plugins() should be an array of plugin
		 * arrays.
		 *
		 * This function is hooked into tgmpa_init, which is fired within the
		 * TGM_Plugin_Activation class constructor.
		 */

	    function tgm_required_plugins()
	    {
			/**
			 * Array of plugin arrays. Required keys are name and slug.
			 * If the source is NOT from the .org repo, then source is also required.
			 */
	    	$plugins = array(
			    array(
			        'name'                  => esc_html__('WPBakery Visual Composer', 'focuson'),
			        'slug'                  => 'js_composer',
			        'source'                => dirname(__FILE__) . '/plugins/visualcomposer.zip',
			        'required'              => true,
			        'force_activation'      => false,
			        'force_deactivation'    => false,
			        'external_url'          => ''
			    )
			 
			);

			/*
			 * Array of configuration settings. Amend each line as needed.
			 *
			 * TGMPA will start providing localized text strings soon. If you already have translations of our standard
			 * strings available, please help us make TGMPA even better by giving us access to these translations or by
			 * sending in a pull-request with .po file(s) with the translations.
			 *
			 * Only uncomment the strings in the config array if you want to customize the strings.
			 */
			$config = array(
				'id'           => 'tgmpa',                 // Unique ID for hashing notices for multiple instances of TGMPA.
				'default_path' => '',                      // Default absolute path to bundled plugins.
				'menu'         => 'tgmpa-install-plugins', // Menu slug.
				'parent_slug'  => 'themes.php',            // Parent menu slug.
				'capability'   => 'edit_theme_options',    // Capability needed to view plugin install page, should be a capability associated with the parent menu used.
				'has_notices'  => true,                    // Show admin notices or not.
				'dismissable'  => true,                    // If false, a user cannot dismiss the nag message.
				'dismiss_msg'  => '',                      // If 'dismissable' is false, this message will be output at top of nag.
				'is_automatic' => false,                   // Automatically activate plugins after installation or not.
				'message'      => '',                      // Message to output right before the plugins table.
				/*
				'strings'      => array(
					'page_title'                      => __( 'Install Required Plugins', 'theme-slug' ),
					'menu_title'                      => __( 'Install Plugins', 'theme-slug' ),
					// <snip>...</snip>
					'nag_type'                        => 'updated', // Determines admin notice type - can only be 'updated', 'update-nag' or 'error'.
				)
				*/
			);

			return tgmpa( $plugins, $config );
	    }

		/**
		 * Wrapper function around cmb2_get_option
		 * @since  0.1.0
		 * @param  string $key     Options array key
		 * @param  mixed  $default Optional default value
		 * @return mixed           Option value
		 */
		static function basetheme_get_option( $key = '', $default = false ) {
			if ( function_exists( 'cmb2_get_option' ) ) {
				// Use cmb2_get_option as it passes through some key filters.
				return cmb2_get_option( 'basetheme_', $key, $default );
			}
			// Fallback to get_option if CMB2 is not loaded yet.
			return get_option( 'basetheme_'.$key, $default );
		}

		static function is_basetheme_page($key = 'page')
		{

			if ( isset( $_GET[ $key ] ) && !empty( $_GET[ $key ] ) ) {
			    return (str_replace('basetheme_', '', esc_html(strip_tags( (string) wp_unslash( $_GET[ $key ] ) )))) ? true : false;
    		}
    		if (self::get_current_post_type()) {
    			return true;
    		}
 			return false;
		}

		/**
		 * gets the current post type in the WordPress Admin
		 */
		static function get_current_post_type() {
		  global $post, $typenow, $current_screen;
		  //we have a post so we can just get the post type from that
		  if ( $post && $post->post_type ) {
		    return $post->post_type;
		  }
		  //check the global $typenow - set in admin.php
		  elseif ( $typenow ) {
		    return $typenow;
		  }
		  //check the global $current_screen object - set in sceen.php
		  elseif ( $current_screen && $current_screen->post_type ) {
		    return $current_screen->post_type;
		  }
		  //check the post_type querystring
		  elseif ( isset( $_GET['post_type'] ) ) {
		    return sanitize_key( $_GET['post_type'] );
		  }
		  //lastly check if post ID is in query string
		  elseif ( isset( $_GET['post'] ) ) {
		    return get_post_type( $_GET['post'] );
		  }
		  //we do not know the post type!
		  return null;
		}

		// hook cmb2 submit button
		public function cmb2_render_callback_for_button( $field, $escaped_value, $object_id, $object_type, $field_type_object ) {
			echo $field_type_object->input( array( 'type' => 'button', 'class'=>'button-secondary' ) );
		}
	}
	if ( !class_exists( 'Config' ) && file_exists( dirname( __FILE__ ) . '/config.php' ) ) {
		require_once( dirname( __FILE__ ) . '/config.php' );
	}
	new BlocksLoaderInit();
?>