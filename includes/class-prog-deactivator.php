<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://www.ee.ethz.ch/de/departement/personen-a-bis-z/person-detail.html?persid=208843
 * @since      1.0.0
 *
 * @package    Prog
 * @subpackage Prog/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Prog
 * @subpackage Prog/includes
 * @author     Lorin Muehlebach <mlorin@ethz.ch>
 */
class Prog_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
        //delete all plugin specific data
        delete_blog_option(get_current_blog_id(), "book_structure");
        
        delete_blog_option(get_current_blog_id(), "lecture_progress");
        
        //delete userdata
		$bookmarks = get_user_meta(get_current_user_id(),'prog_bookmark',true);
		
		var_dump($bookmarks);
        
        if(!is_array($bookmarks) || count($bookmarks)==1){
            delete_user_meta(get_current_user_id(), 'prog_bookmark');
        }else{
			unset($bookmarks[get_current_blog_id()]);
			update_user_meta( get_current_user_id(), 'prog_bookmark', $bookmarks);
		}	
        
	}

}
