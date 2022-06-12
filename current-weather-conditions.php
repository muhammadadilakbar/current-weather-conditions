<?php
/**
* Plugin Name: Current Weather Conditions
* Plugin URI: https://www.github.com/muhammadadilakbar/current-weather-conditions
* Description: A plugin which fetches current weather conditions from AccuWeather API
* Version: 1.0.0
* Requires at least: 4.5
* Requires PHP: 5.6
* Author: Muhammad-Adil Akbar
* Author URI: https://www.github.com/muhammadadilakbar/
* License: GPL v2 or later
* License URI: https://www.gnu.org/licenses/gpl-2.0.html
* Text Domain: bspdi_cuwe
*/

namespace BaseSpeedDigital\CurrentWeather;

if ( ! defined( 'BSPDI_CUWE_DIR' ) ) {
	define( 'BSPDI_CUWE_DIR', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'BSPDI_CUWE_URL' ) ) {
	define( 'BSPDI_CUWE_URL', plugin_dir_url( __FILE__ ) );
}

\register_activation_hook( __FILE__, function() {
	require_once( BSPDI_CUWE_DIR . 'src/Activation.php' );
	Activation::activate();
});

\register_deactivation_hook( __FILE__, function() {
	require_once( BSPDI_CUWE_DIR . 'src/Deactivation.php' );
	Deactivation::deactivate();
});

require_once( BSPDI_CUWE_DIR . 'src/CurrentWeather.php' );

new CurrentWeather();