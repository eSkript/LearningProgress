<?php

/**
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.ee.ethz.ch/de/departement/personen-a-bis-z/person-detail.html?persid=208843
 * @since             0.0.1
 * @package           Prog
 *
 * @wordpress-plugin
 * Plugin Name:       Learning progress
 * Plugin URI:        https://mskript.ethz.ch/
 * Description:       displays the learning/reading progress in a PressBook of a user 
 * Version:           0.0.2
 * Author:            Lorin Muehlebach
 * Author URI:        https://www.ee.ethz.ch/de/departement/personen-a-bis-z/person-detail.html?persid=208843
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       prog
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-prog-activator.php
 */
function activate_prog() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-prog-activator.php';
	Prog_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-prog-deactivator.php
 */
function deactivate_prog() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-prog-deactivator.php';
	Prog_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_prog' );
register_deactivation_hook( __FILE__, 'deactivate_prog' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-prog.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    0.0.1
 */
function run_prog() {

	$plugin = new Prog();
	$plugin->run();

}
run_prog();

function prog_cover(){
    //echo "learning progress active";
    //include('test.php');
}
