<?php

/**
 * Plugin Name: Castlegate IT WP Maintenance Mode
 * Plugin URI:  https://github.com/castlegateit/cgit-wp-maintenace-mode
 * Description: Basic maintenance mode plugin for WordPress and WooCommerce.
 * Version:     1.0.0
 * Author:      Castlegate IT
 * Author URI:  https://www.castlegateit.co.uk/
 * License:     MIT
 * Update URI:  https://github.com/castlegateit/cgit-wp-maintenace-mode
 */

use Castlegate\MaintenanceMode\Plugin;

if (!defined('ABSPATH')) {
    wp_die('Access denied');
}

define('CGIT_WP_MAINTENANCE_MODE_VERSION', '1.0.0');
define('CGIT_WP_MAINTENANCE_MODE_PLUGIN_FILE', __FILE__);
define('CGIT_WP_MAINTENANCE_MODE_PLUGIN_DIR', __DIR__);

require_once __DIR__ . '/vendor/autoload.php';

Plugin::init();
