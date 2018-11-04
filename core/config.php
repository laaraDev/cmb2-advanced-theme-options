<?php
/**
* Config class
*/

class Config
{
	// declaration of global vars
	static $prefix = NULL;
	static $domain_name = NULL;
	static $theme_data = NULL;
	static $general_options = NULL;
	static $social_options = NULL;
	static $newsletter_options = NULL;
	static $core_dir_uri = NULL;
	static $core_assets_css = NULL;
	static $core_assets_js = NULL;
	static $uri = NULL;

	function __construct()
	{
		add_action('wp_loaded', array($this, 'global_vars') );
	}

	static function global_vars() {
		// init global vars
		if ( is_null(self::$prefix) ) {
			self::$prefix = self::prefix();
		}
		if ( is_null(self::$domain_name) ) {
			self::$domain_name = self::domain_name();
		}
		if ( is_null(self::$theme_data) ) {
			self::$theme_data = self::theme_data();
		}
		if ( is_null(self::$general_options) ) {
			self::$general_options = self::basetheme_get_option('general_options');
		}
		if ( is_null(self::$social_options) ) {
			self::$social_options = self::basetheme_get_option('social_options');
		}
		if ( is_null(self::$newsletter_options) ) {
			self::$newsletter_options = self::basetheme_get_option('newsletter_options');
		}
		if ( is_null(self::$core_dir_uri) ) {
			self::$core_dir_uri = self::core_dir_uri();
		}
		// if ( is_null(self::$core_assets_css) ) {
		// 	self::$core_assets_css = self::core_assets_css();
		// }
		// if ( is_null(self::$core_assets_js) ) {
		// 	self::$core_assets_js = self::core_assets_js();
		// }
	}

	static function prefix()
	{
		return 'basetheme_';
	}

	static function domain_name()
	{
		return strtoupper(str_replace(' ', '_', get_bloginfo( 'name' )));
	}

	static function theme_data()
	{
		return get_option('theme_datas');
	}

	static function core_dir()
	{
		$actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	}

	static function core_dir_uri()
	{
		return get_template_directory_uri() . '/inc/core/';
	}

	static function core_assets_css($filename = 'basetheme')
	{
		return self::core_dir_uri() . 'assets/css/'.$filename.'.css';
	}

	static function core_assets_js($filename = 'basetheme')
	{
		return self::core_dir_uri() . 'assets/js/'.$filename.'.js';
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
			return cmb2_get_option( self::prefix(), $key, $default );
		}
		// Fallback to get_option if CMB2 is not loaded yet.
		return get_option( self::prefix().$key, $default );
	}
}
Config::global_vars();
?>