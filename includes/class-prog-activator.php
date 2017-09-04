<?php

/**
 * Fired during plugin activation
 *
 * @link       https://www.ee.ethz.ch/de/departement/personen-a-bis-z/person-detail.html?persid=208843
 * @since      1.0.0
 *
 * @package    Prog
 * @subpackage Prog/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Prog
 * @subpackage Prog/includes
 * @author     Lorin Muehlebach <mlorin@ethz.ch>
 */
class Prog_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		/*
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-prog-admin.php';
		$plugin_admin = new Prog_Admin( "prog", "1.0.0");
		$plugin_admin->recalculate_statistics();
		*/
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/functions.php';
        
        update_blog_option(get_current_blog_id(), "book_structure", get_book_lenght(pb_get_book_structure()));
	}

}
