# This plugin contain : 
- Blocks loader helps to create and load blocks
- Theme options

# Blocks loader init
# add this to you theme functions to initialize theme options

```
if ( !class_exists( 'BlocksLoaderInit' ) && file_exists( dirname( __DIR__ ) . '\core\init.php' ) ) {
  require_once( dirname( __DIR__ ) . '\core\init.php' );
}
```
- Create block parameter from Blocks post type.
For example your block name : Contact
- Create file called contact.php in blocks folder (If your block name contain space for exemple : Contact us the file name will be contact_us.php).
- Block parameter will be disponible in global variable : $BlockContact (If your block name contain space for exemple : Contact us the block parameter will be disponible in global variable : $BlockContact_us).
You can load this block anywhere you want just like :

```
$loader = BlocksLoaderInit::$BlocksLoader;
$loader->getContact();
// If your block name contain space for exemple : Contact us 
$loader->getContact_us();
```
Or you can call it from a shortcode

```
do_shortcode( '[BlockContact]' );
// If your block name contain space for exemple : Contact us
do_shortcode( '[BlockContact_us]' );
```
You can override block parameter from shortcode like that :
```
do_shortcode( '[BlockContact block_title="test contact"]' );
// If your block name contain space for exemple : Contact us
do_shortcode( '[BlockContact_us block_title="test contact"]' );
```
- block_title => block title parameter key

You can passe extras data with shortcode attributes
