<?php
/**
Plugin Name: SVG Title
Plugin URI: https://github.com/VladimirGavrilovskih/svg_title
Description: Text to SVG title edit
Version: 1.0
Requires at least: 4.7
Requires PHP: 5.6
Author: vgavrilovskih
Author URI:
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Text Domain: svg-title
Domain Path: /languages/
*/

define('SVGT_VERSION', '1.0');

define('SVGT_PLUGIN', __FILE__);

define('SVGT_PLUGIN_BASENAME', plugin_basename(SVGT_PLUGIN));

//define('SVGT_PLUGIN_NAME', trim(dirname(SVGT_PLUGIN_BASENAME), '/'));

define('SVGT_PLUGIN_DIR', untrailingslashit(dirname(SVGT_PLUGIN)));

require_once SVGT_PLUGIN_DIR . '/settings.php';
