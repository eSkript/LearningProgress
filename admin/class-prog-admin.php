<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.ee.ethz.ch/de/departement/personen-a-bis-z/person-detail.html?persid=208843
 * @since      1.0.0
 *
 * @package    Prog
 * @subpackage Prog/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Prog
 * @subpackage Prog/admin
 * @author     Lorin Muehlebach <mlorin@ethz.ch>
 */
class Prog_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Prog_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Prog_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/prog-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Prog_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Prog_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/prog-admin.js', array( 'jquery' ), $this->version, false );

	}
    
    public function add_plugin_admin_menu() {

        /*
         * Add a settings page for this plugin to the Settings menu.
         *
         * NOTE:  Alternative menu locations are available via WordPress administration menu functions.
         *
         *        Administration Menus: http://codex.wordpress.org/Administration_Menus
         *
         */
        add_options_page( 'Learning Progress Settings', 'Learning Progress', 'manage_options', $this->plugin_name,array($this, 'display_plugin_setup_page')
        );
    }
    
    /**
     * Add settings action link to the plugins page.
     */
    public function add_action_links( $links ) {
       $settings_link = array(
        '<a href="' . admin_url( 'options-general.php?page=' . $this->plugin_name ) . '">' . __('Settings', $this->plugin_name) . '</a>',
       );
       return array_merge(  $settings_link, $links );

    }
    
    /**
     * Render the settings page for this plugin.
     */
    public function display_plugin_setup_page() {
        include_once( 'partials/prog-admin-display.php' );
    }
    
    public function options_update() {
        register_setting($this->plugin_name, $this->plugin_name, array($this, 'validate'));
     }
    
    /**
     * Validate input from settings page
     */
    public function validate($input) {
        // All checkboxes inputs        
        $valid = array();

        //Cleanup
        $valid['debug'] = (isset($input['debug']) && !empty($input['debug'])) ? 1 : 0;
        $valid['lecture_progress'] = (isset($input['lecture_progress']) && !empty($input['lecture_progress'])) ? 1: 0;
        
        $valid['debug'] = 1;

        return $valid;
    }
	
	
	//recalculate book statistics
	public function recalculate_statistics_settings() {
        //get_book_lenght(pb_get_book_structure());
        
        echo update_blog_option(get_current_blog_id(), "book_structure", get_book_lenght(pb_get_book_structure()));
        
        
        if (isset($_SERVER["HTTP_REFERER"])) {
            header("Location: " . $_SERVER["HTTP_REFERER"]);
        }
        
		die();
        
	}
    
    public function get_book_structure_and_return_to_console(){
        $chapters = "";
        $book = pb_get_book_structure();
        
        foreach ($book['part'] as $part) {
            foreach ($part['chapters'] as $chapter){
                $chapters .= "console.log(".json_encode(get_post($chapter['ID'])).");";
            }
        }
        
        echo "<html><script>console.log(".json_encode($book).");".$chapters."</script>
        Book Structure is displayed in Console 
        <a href=".$_SERVER["HTTP_REFERER"].">back</a></html>";
        //Todo display chapter Data
    }
    
    
}
