<?php
	/**
	 * ShortcodeGenerator
	 */
	class ShortcodeGenerator
	{
		protected $settings = array();

		function __construct($settings = array())
		{
			$this->settings = $settings;
		}

		public function __call( $name, $arguments )
	    {
	        if( in_array( $name, array_keys( $this->settings ) ) )
	        {
	        	$args = wp_parse_args( $arguments[0], $this->settings[$name] );
	        	$GLOBALS[$this->buildBlockKey($name)] = $args;
	        	require dirname(__DIR__)."/blocks/".$name.".php";
	        }
	    }

	    public function generate()
	    {
	        foreach( $this->settings as $shortcode => $option )
	        {
	          	add_shortcode( $this->buildBlockKey($shortcode), array( $this, $shortcode ) );
	        }
	    }

	    public function buildBlockKey($method)
	    {
	        return "Block".ucfirst($method);
	    }
	}
?>