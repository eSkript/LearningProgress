<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://www.ee.ethz.ch/de/departement/personen-a-bis-z/person-detail.html?persid=208843
 * @since      1.0.0
 *
 * @package    Prog
 * @subpackage Prog/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Prog
 * @subpackage Prog/includes
 * @author     Lorin Muehlebach <mlorin@ethz.ch>
 */
class Prog_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'prog',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
