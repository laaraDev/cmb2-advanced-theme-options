<?php
/**
 * CMB2 Tabbed Theme Options
 *
 * @author    Arushad Ahmed <@dash8x, contact@arushad.org>
 * @link      https://arushad.org/how-to-create-a-tabbed-options-page-for-your-wordpress-theme-using-cmb2
 * @version   0.1.0
 */

class BaseTheme {
 
    private $prefix = 'basetheme_';
    private $key = 'basetheme_options';
    protected $option_metabox = array();
    protected $title = '';
    protected $domaine = 'cmb2';
    protected $button = '';
    protected $parent_menu_title = '';
    protected $options_pages = array();
 
    /**
     * Constructor
     * @since 0.1.0
     */
    public function __construct() {
        // Set our title
        $this->title = __( 'Basetheme v1.0', $this->domaine );
        $this->parent_menu_title = __( 'Theme options', $this->domaine );
        $this->domaine = __( 'cmb2', $this->domaine );
        $this->button = __( 'Save theme options', $this->domaine );

    }
 
    /**
     * Initiate our hooks
     * @since 0.1.0
     */
    public function hooks() {
        add_action( 'admin_init', array( $this, 'init' ) );
        add_action( 'admin_menu', array( $this, 'add_options_page' ) ); //create tab pages
		// add_action( 'admin_menu', array($this, 'remove_add_submenu_from_posttype'));
    }
 
    /**
     * Register our setting tabs to WP
     * @since  0.1.0
     */
    public function init() {
    	$option_tabs = self::option_fields();
        foreach ($option_tabs as $index => $option_tab) {
        	register_setting( $option_tab['id'], $option_tab['id'] );
        }
    }
 
    /**
     * Add menu options page
     * @since 0.1.0
     */
    public function add_options_page() {        
        $option_tabs = self::option_fields();
        foreach ($option_tabs as $index => $option_tab) {
        	if ( $index == 0) {
        		// add_submenu_page('edit.php?post_type=blocks', $this->parent_menu_title, $this->parent_menu_title, 'edit_posts', $option_tab['id'], array( $this, 'admin_page_display' ));
        		// $this->options_pages[] = add_menu_page( $this->title, $this->parent_menu_title, 'manage_options', $option_tab['id'], array( $this, 'admin_page_display' ) ); //Link admin menu to first tab
        		// $this->options_pages[] = add_submenu_page( $option_tabs[0]['id'], 'Blocks', 'Blocks generator', 'manage_options', $option_tab['id'] ); 
        		// add_submenu_page( $option_tabs[0]['id'], $this->title, $option_tab['title'], 'manage_options', $option_tab['id'], array( $this, 'admin_page_display' ) ); //Duplicate menu link for first submenu page
        	} else {
        	}
        	$this->options_pages[] = add_submenu_page( 'edit.php?post_type=blocks', $this->title, $option_tab['title'], 'manage_options', $option_tab['id'], array( $this, 'admin_page_display' ) );
        }
    }
 
    /**
     * Admin page markup. Mostly handled by CMB2
     * @since  0.1.0
     */
    public function admin_page_display() {
    	$option_tabs = self::option_fields(); //get all option tabs
    	$tab_forms = array();     	   	
        ?>
        <div class="wrap cmb2_options_page <?php echo $this->key; ?>">
        	<div class="row">
            	<h2 class="col-md-12"><?php echo esc_html( get_admin_page_title() ); ?></h2>
            	<button type="button" class="button-primary button-danger active-btn-save"><?php echo $this->button; ?></button>
        	</div>      	
            <!-- Options Page Nav Tabs -->
            <div class="row fitHeight">
				<div class="nav flex-column nav-pills col-md-2" id="v-pills-tab" role="tablist" aria-orientation="vertical">
					<?php foreach ($option_tabs as $option_tab) :
	            		$tab_slug = $option_tab['id'];
	            		$nav_class = 'nav-tab';
	            		if ( $tab_slug == $_GET['page'] ) {
	            			$nav_class .= ' active'; //add active class to current tab
	            			$tab_forms[] = $option_tab; //add current tab to forms to be rendered
	            		}
	            	?>  
						<a class="nav-link <?php echo $nav_class; ?>" id="v-pills-<?php echo $tab_slug ?>-tab" data-toggle="pill1" href="<?php menu_page_url( $tab_slug ); ?>" role="tab" aria-controls="v-pills-<?php echo $tab_slug ?>" aria-selected="true"><?php esc_attr_e($option_tab['title']); ?></a>
					<?php endforeach; ?>
				</div>
				<div class="tab-content col-md-10" id="v-pills-tabContent">
					<?php foreach ($tab_forms as $tab_form) : //render all tab forms (normaly just 1 form) ?>
						<div class="tab-pane fade show active" id="v-pills-<?php esc_attr_e($tab_form['id']); ?>" role="tabpanel" aria-labelledby="v-pills-<?php esc_attr_e($tab_form['id']); ?>-tab">
							<?php cmb2_metabox_form( $tab_form, $tab_form['id'] , array(
									'save_button' => __( $tab_form['save_button'], $this->domaine )
									) ); ?>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
			<div class="credits col-md-12">
				<div class="basetheme-right">All right reserved <?php echo date('Y'); ?> &copy;</div>
				<div class="basetheme-author">By Laarabi Simohammed</div>
			</div>
        </div>
        <?php
    }
 
    /**
     * Defines the theme option metabox and field configuration
     * @since  0.1.0
     * @return array
     */
    public function option_fields() {
 
        // Only need to initiate the array once per page-load
        if ( ! empty( $this->option_metabox ) ) {
            return $this->option_metabox;
        }
 		// general settings
        $this->option_metabox[] = array(
            'id'         => $this->prefix.'general_options', //id used as tab page slug, must be unique
            'title'      => 'General',
            'show_on'    => array( 'key' => 'options-page', 'value' => array( 'general_options' ), ), //value must be same as id
            'show_names' => true,
            'save_button'     => esc_html__( $this->button, $this->domaine), // The text for the options-page save button. Defaults to 'Save'.
			'message_cb'   => 'basetheme_options_message_callback',
            'fields'     => array(
				array(
					'name' => __('Header Logo', $this->domaine),
					'desc' => __('Logo to be displayed in the header.', $this->domaine),
					'id' => $this->prefix.'header_logo', //each field id must be unique
					'default' => '',
					'type' => 'file',
				),	
				array(
					'name' => __('Footer Logo', $this->domaine),
					'desc' => __('Logo to be displayed in the footer.', $this->domaine),
					'id' => $this->prefix.'footer_logo', //each field id must be unique
					'default' => '',
					'type' => 'file',
				),		
				array(
					'name' => __('Login Logo', $this->domaine),
					'desc' => __('Logo to be displayed in the login page (320x120)', $this->domaine),
					'id' => $this->prefix.'login_logo',
					'default' => '',
					'type' => 'file',
				),
				array(
					'name' => __('Favicon', $this->domaine),
					'desc' => __('Site favicon (32x32)', $this->domaine),
					'id' => $this->prefix.'favicon',
					'default' => '',
					'type' => 'file',
				),
				array(
					'name' => __('Footer description', $this->domaine),
					'desc' => __('Descrption to display in site footer', $this->domaine),
					'id' => $this->prefix.'footer_desc',
					'default' => '',				
					'type' => 'wysiwyg',
					'options' => array(
						'textarea_rows' => 3,
					)
				),
				array(
					'name' => __('Copyright', $this->domaine),
					'desc' => __('Copyright to display in site footer', $this->domaine),
					'id' => $this->prefix.'footer_copyright',
					'default' => '',				
					'type' => 'wysiwyg',
					'options' => array(
						'textarea_rows' => 3,
					)
				),
				array(
					'name' => __( 'SEO', $this->domaine ),
					'desc' => __( 'Search Engine Optimization Settings.', $this->domaine ),
					'id'   => $this->prefix.'branding_title', //field id must be unique
					'type' => 'title',
				),
				array(
					'name' => __('Site Keywords', $this->domaine),
					'desc' => __('Keywords describing this site, comma separated.', $this->domaine),
					'id' => $this->prefix.'site_keywords',
					'default' => '',				
					'type' => 'textarea_small',
					'attributes' => array(
						'data-keywords' => true
					),
				),
				array(
					'name' => __('Preloader', $this->domaine),
					'desc' => __('Enable site preloader', $this->domaine),
					'id' => $this->prefix.'enable_preloader',
					'default' => '',				
					'type' => 'checkbox',
				),
				array(
					'name' => __('WP head hook', $this->domaine),
					'desc' => __('Hook wp head to add custom scripts and css', $this->domaine),
					'id' => $this->prefix.'hook_wp_head',
					'default' => '',				
					'type' => 'textarea_code',
					'options' => array(
						'textarea_rows' => 3,
					)
				),
				array(
					'name' => __('WP footer hook', $this->domaine),
					'desc' => __('Hook wp footer to add custom scripts and css', $this->domaine),
					'id' => $this->prefix.'hook_wp_footer',
					'default' => '',				
					'type' => 'textarea_code',
					'options' => array(
						'textarea_rows' => 3,
					)
				),
			)
        );

        // generated blocks
        $blocks = get_posts(array('post_type' => 'blocks', 'posts_per_page' => -1, 'order' => 'ASC', 'orderby' => 'ID'));
        foreach ($blocks as $block) {
        	$block_id = strtolower(str_replace('-', '_', $block->post_name));
	        $this->option_metabox[] = array(
	            'id'         => $this->prefix . $block_id, //id used as tab page slug, must be unique
	            'title'      => $block->post_title,
	            'show_on'    => array( 'key' => 'options-page', 'value' => array( $block_id ), ), //value must be same as id
	            'show_names' => true,
	            'save_button'     => esc_html__( $this->button, $this->domaine), // The text for the options-page save button. Defaults to 'Save'.
				'message_cb'   => 'basetheme_options_message_callback',
	            'fields'     => $this->get_fields_args(get_post_meta( $block->ID, 'basetheme_blocks_fields', true ))
	        );
        }

 		// newsletter settings
        $this->option_metabox[] = array(
            'id'         => $this->prefix.'newsletter_options', //id used as tab page slug, must be unique
            'title'      => 'Newsletter',
            'show_on'    => array( 'key' => 'options-page', 'value' => array( 'newsletter_options' ), ), //value must be same as id
            'show_names' => true,
            'save_button'     => esc_html__( $this->button, $this->domaine), // The text for the options-page save button. Defaults to 'Save'.
			'message_cb'   => 'basetheme_options_message_callback',
            'fields'     => array(
				array(
					'name' => __( 'Newsletter', $this->domaine ),
					'desc' => __( 'Newsletter Settings.', $this->domaine ),
					'id'   => $this->prefix.'newsletter_title', //field id must be unique
					'type' => 'title',
				),
				array(
					'name' => __('Display newsletter', $this->domaine),
					'desc' => __('Check this to allow newsletter on this site', $this->domaine),
					'id' => $this->prefix.'show_newsletter',
					'default' => '',				
					'type' => 'checkbox',
				),
				array(
					'name' => __('Newsletter description', $this->domaine),
					'desc' => __('Add newsletter description', $this->domaine),
					'id' => $this->prefix.'newsletter_description',
					'default' => '',				
					'type' => 'wysiwyg',
					'options' => array(
						'textarea_rows' => 3,
					)
				),
				array(
					'name' => __('Email', $this->domaine),
					'desc' => __('Add email where recieve user subscribe notification', $this->domaine),
					'id' => $this->prefix.'newsletter_email',
					'default' => '',				
					'type' => 'text',
				),
				array(
					'name' => __('Email subject', $this->domaine),
					'desc' => __('Add email subject where recieve user subscribe notification', $this->domaine),
					'id' => $this->prefix.'newsletter_email_subject',
					'default' => '',				
					'type' => 'text',
				),
				array(
					'name' => __('Newsletter email content', $this->domaine),
					'desc' => __('Add newsletter email content', $this->domaine),
					'id' => $this->prefix.'newsletter_email_content',
					'default' => '',				
					'type' => 'wysiwyg',
					'options' => array(
						'textarea_rows' => 3,
					)
				),
				array(
					'name' => __('Newsletter registration success', $this->domaine),
					'desc' => __('Add newsletter registration success', $this->domaine),
					'id' => $this->prefix.'newsletter_registration_success',
					'default' => '',				
					'type' => 'wysiwyg',
					'options' => array(
						'textarea_rows' => 3,
					)
				),
				array(
					'name' => __('Newsletter registration error', $this->domaine),
					'desc' => __('Add newsletter registration error', $this->domaine),
					'id' => $this->prefix.'newsletter_registration_error',
					'default' => '',				
					'type' => 'wysiwyg',
					'options' => array(
						'textarea_rows' => 3,
					)
				),
				array(
					'name' => __('Newsletter wrong email', $this->domaine),
					'desc' => __('Add newsletter wrong email', $this->domaine),
					'id' => $this->prefix.'newsletter_wrong_email',
					'default' => '',				
					'type' => 'wysiwyg',
					'options' => array(
						'textarea_rows' => 3,
					)
				)
			)
        );

 		// social params
        $this->option_metabox[] = array(
            'id'         => $this->prefix.'social_options',
            'title'      => 'Social Media',
            'show_on'    => array( 'key' => 'options-page', 'value' => array( 'social_options' ), ),
            'show_names' => true,
            'save_button'     => esc_html__( $this->button, $this->domaine), // The text for the options-page save button. Defaults to 'Save'.
			'message_cb'   => 'basetheme_options_message_callback',
 			'disable_settings_errors' => false, // On settings pages (not options-general.php sub-pages), allows disabling.
			'fields'     => array(
				array(
					'name' => __('Facebook', $this->domaine),
					'desc' => __('Link of facebook page or page.', $this->domaine),
					'id' => $this->prefix.'facebook',
					'default' => '#',					
					'type' => 'text'
				),
				array(
					'name' => __('Instagram', $this->domaine),
					'desc' => __('Link of instagram page or page.', $this->domaine),
					'id' => $this->prefix.'instagram',
					'default' => '#',					
					'type' => 'text'
				),
				array(
					'name' => __('Twitter', $this->domaine),
					'desc' => __('Link of twitter profile.', $this->domaine),
					'id' => $this->prefix.'twitter',
					'default' => '#',					
					'type' => 'text'
				),
				array(
					'name' => __('Youtube', $this->domaine),
					'desc' => __('Link of youtube channel.', $this->domaine),
					'id' => $this->prefix.'youtube',
					'default' => '#',					
					'type' => 'text'
				),
				array(
					'name' => __('Flickr', $this->domaine),
					'desc' => __('Link of flickr profile.', $this->domaine),
					'id' => $this->prefix.'flickr',
					'default' => '#',					
					'type' => 'text'
				),
				array(
					'name' => __('Google+', $this->domaine),
					'desc' => __('Link of google+ profile.', $this->domaine),
					'id' => $this->prefix.'google_plus',
					'default' => '#',					
					'type' => 'text'
				)
			)
        );
 		
 		// export import settings
        $this->option_metabox[] = array(
            'id'         => $this->prefix.'export_import_options',
            'title'      => 'Export / Import',
            'show_on'    => array( 'key' => 'options-page', 'value' => array( 'export_import_options' ), ),
            'show_names' => true,
            'save_button'     => esc_html__( $this->button, $this->domaine), // The text for the options-page save button. Defaults to 'Save'.
			'message_cb'   => 'basetheme_options_message_callback',
            'fields'     => array(
            	array(
					'name'             => esc_html__( 'Export settings', 'cmb2' ),
					'desc'             => esc_html__( 'Copy all theme options settings', 'cmb2' ),
					'id'               => $this->prefix . 'export',
					'type' => 'textarea',
					'default' => $this->get_theme_options_settings(),
					'options' => array(
						'textarea_rows' => 10,
					)
				),
				array(
					'name'             => esc_html__( 'Import settings', 'cmb2' ),
					'desc'             => esc_html__( 'Import all theme options settings', 'cmb2' ),
					'id'               => $this->prefix . 'import',
					'type' => 'textarea',
					'default' => '',
					'options' => array(
						'textarea_rows' => 10,
					)
				),
				array(
					'name'             => esc_html__( 'Add theme settings', 'cmb2' ),
					'desc'             => esc_html__( 'Click to add theme settings', 'cmb2' ),
					'id'               => $this->prefix . 'add_options_button',
					'type' => 'button',
					'default' => 'Add options'
				)
			)
        );

        return $this->option_metabox;
    }
 	
 	public function get_fields_args($fields_args)
 	{
 		$args = [];
 		$default_group_options = [];
 		if (empty($fields_args)) return $args;
 		
 		foreach ($fields_args as $key => $val) {
	 		$field = $this->generate_fields_args($val);
	 		if (!empty($val['groups'])) {
	 			foreach ($val['groups'] as $k => $group) {
					$field['fields'][] = $this->generate_fields_args($group);
		 		}
			}
	 		$args[] = $field;
 		}
 		return $args;
 	}

 	public function generate_fields_args($field)
 	{
 		$args = [];
 		$default_group_options = [];
 		
 		$field_args = array(
			'name' => __($field['field_label'], $this->domaine),
			'desc' => __((!empty($field['field_description'])) ? $field['field_description'] : '', $this->domaine),
			'id' => $field['field_id'],
			'default' => (!empty($field['field_default'])) ? $field['field_default'] : '',				
			'type' => $field['field_type'],
		);

		if (!empty($field['field_type']) && $field['field_type'] == 'group') {
	 		$default_group_options = array(
				'group_title' => esc_html__( 'Entry {#}', 'cmb2' ), // {#} gets replaced by row number
				'add_button' => esc_html__( 'Add New', 'cmb2' ),
				'remove_button' => esc_html__( 'Remove', 'cmb2' ),
				'sortable' => true, // beta
				'closed' => false, // true to have the groups closed by default
			);

		}

		if (!empty($field['options'])) {
			foreach ($field['options'] as $key => $value) {
				$field_args['options'][$value['option_key']] = $this->parse_value($value['option_value']);
			}
			// merge default options
	 		$field_args['options'] = wp_parse_args( $field_args['options'], $default_group_options );
		}

		if (!empty($field['attributes'])) {
			foreach ($field['attributes'] as $key => $value) {
				$field_args['attributes'][$value['attribute_key']] = $this->parse_value($value['attribute_value']);
			}
		}

		$field_args['attributes']['required'] = (!empty($field['field_required'])) ? $field['field_required'] : 'off';
		return $field_args;
 	}

 	public function parse_value($value)
 	{
 		$value = trim($value);
 		switch ($value) {
 			case 'true':
 				$value = 1;
 				break;
 			case 'false':
 				$value = 0;
 				break;
 			case true:
 				$value = 1;
 				break;
 			case false:
 				$value = 0;
 				break;
 			
 			default:
 				$value;
 				break;
 		}
 		return $value;
 	}
 	// get basetheme saved options as json encoded
 	public function get_theme_options_settings()
 	{
 		$theme_options = [];
 		// $all_options = get_alloptions();
 		$all_options = wp_load_alloptions();
 		if (!empty($this->option_metabox)) {
	 		foreach ($this->option_metabox as $key => $val) {
	 			if (substr($val['id'], 0, strlen(Config::$prefix)) === Config::$prefix && $val['id'] != 'basetheme_export_import_options' && isset($all_options[$val['id']])) {
	 				$theme_options[$val['id']] = $all_options[$val['id']];
	 			}
	 		}
 		}
 		return base64_encode(serialize($theme_options));
 	}

 	public function decode_basetheme_options()
 	{
 		return unserialize(base64_decode($this->get_theme_options_settings()));
 	}

    /**
     * Returns the option key for a given field id
     * @since  0.1.0
     * @return array
     */
    public function get_option_key($field_id) {
    	$option_tabs = $this->option_fields();
    	foreach ($option_tabs as $option_tab) { //search all tabs
    		foreach ($option_tab['fields'] as $field) { //search all fields
    			if ($field['id'] == $field_id) {
    				return $option_tab['id'];
    			}
    		}
    	}
    	return $this->key; //return default key if field id not found
    }
 
    /**
     * Public getter method for retrieving protected/private variables
     * @since  0.1.0
     * @param  string  $field Field to retrieve
     * @return mixed          Field value or exception is thrown
     */
    public function __get( $field ) {
        // Allowed fields to retrieve
        if ( in_array( $field, array( 'key', 'fields', 'title', 'options_pages' ), true ) ) {
            return $this->{$field};
        }
        if ( 'option_metabox' === $field ) {
            return $this->option_fields();
        }
 		
        throw new Exception( 'Invalid property: ' . $field );
    }

    public function packageID()
    {
    	$saved_id = get_option( 'basetheme_package_id' );
    	if (!empty($saved_id)) {
    		$saved_id = $saved_id+1; 
    		update_option( 'basetheme_package_id', $saved_id );
    	}else{
    		update_option( 'basetheme_package_id', 1 );
    		$saved_id = get_option( 'basetheme_package_id' );
    	}
    	return $saved_id;
    }

    public function remove_add_submenu_from_posttype() {
	    remove_submenu_page('edit.php?post_type=blocks','edit.php?post_type=blocks');
	    remove_submenu_page('edit.php?post_type=blocks','post-new.php?post_type=blocks');
	}
}
 
// Get it started
$baseTheme = new BaseTheme();
$baseTheme->hooks();

/**
 * Callback to define the optionss-saved message.
 *
 * @param CMB2  $cmb The CMB2 object.
 * @param array $args {
 *     An array of message arguments
 *
 *     @type bool   $is_options_page Whether current page is this options page.
 *     @type bool   $should_notify   Whether options were saved and we should be notified.
 *     @type bool   $is_updated      Whether options were updated with save (or stayed the same).
 *     @type string $setting         For add_settings_error(), Slug title of the setting to which
 *                                   this error applies.
 *     @type string $code            For add_settings_error(), Slug-name to identify the error.
 *                                   Used as part of 'id' attribute in HTML output.
 *     @type string $message         For add_settings_error(), The formatted message text to display
 *                                   to the user (will be shown inside styled `<div>` and `<p>` tags).
 *                                   Will be 'Settings updated.' if $is_updated is true, else 'Nothing to update.'
 *     @type string $type            For add_settings_error(), Message type, controls HTML class.
 *                                   Accepts 'error', 'updated', '', 'notice-warning', etc.
 *                                   Will be 'updated' if $is_updated is true, else 'notice-warning'.
 * }
 */
foreach ($baseTheme->option_metabox as $key) {
	add_action( 'update_option_'.$key['id'], 'basetheme_options_message_callback', 10 );
	// add_action('updated_option', 'basetheme_options_message_callback', 10, 3);
}

function basetheme_options_message_callback($old_value, $new_value = '') {
	print '<div class="updated notice"><p><b>&mdash; Updated!:</b> theme options up to date</p></div>';
}
/**
 * Wrapper function around cmb2_get_option
 * @since  0.1.0
 * @param  string  $key Options array key
 * @return mixed        Option value
 */

function basetheme_option( $key = '' ) {
    global $baseTheme;
    return cmb2_get_option( $baseTheme->get_option_key($key), $key );
}

function basetheme_options_notices() {
	$basetheme_options = get_option( 'basetheme_general_options' );
	if ( empty($basetheme_options) ) {
		print '<div class="error notice"><p><b>Warning:</b> No configuration found for Theme options. You need to set the Theme options configuration</p></div>';
		return;
	}
}
add_action( 'admin_notices', 'basetheme_options_notices' );
?>