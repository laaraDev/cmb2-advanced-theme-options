# This plugin contain : 
- Blocks loader helps to create and load blocks
- Theme options

# Blocks loader init
# add this to you theme functions to initialize theme options

<p>if ( !class_exists( 'BlocksLoaderInit' ) && file_exists( dirname( __DIR__ ) . '\inc\core\init.php' ) ) {</p>
    <p>require_once( dirname( __DIR__ ) . '\inc\core\init.php' );</p>
<p>}</p>
