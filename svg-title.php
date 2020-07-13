<?php
/*
Plugin Name: SVG Title
Plugin URI:
Description: Text to SVG title edit
Author: Vladimir G
Author URI: 
Text Domain: svg-title
Domain Path: /languages/
Version: 1.0
*/

define('SVGT_VERSION', '1.0');

define('SVGT_PLUGIN', __FILE__);

define('SVGT_PLUGIN_BASENAME', plugin_basename(SVGT_PLUGIN));

//define('SVGT_PLUGIN_NAME', trim(dirname(SVGT_PLUGIN_BASENAME), '/'));

define('SVGT_PLUGIN_DIR', untrailingslashit(dirname(SVGT_PLUGIN)));

require_once SVGT_PLUGIN_DIR . '/settings.php';
