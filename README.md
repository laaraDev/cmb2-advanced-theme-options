# This plugin contain : 
- Blocks loader helps to create and load blocks
- Theme options

# Blocks loader init
# add this to you theme functions to initialize theme options

<code>
    if ( !class_exists( 'BlocksLoaderInit' ) && file_exists( dirname( __DIR__ ) . '\inc\core\init.php' ) ) {
        require_once( dirname( __DIR__ ) . '\inc\core\init.php' );
    }
</code>
