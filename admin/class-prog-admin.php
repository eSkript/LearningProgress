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
	public function recalculate_statistics() {
    	//echo "<script>console.log(" . json_encode($this->get_book_lenght(pb_get_book_structure())).")</script>";
        
        //TODO security noonce
        
        echo update_blog_option(get_current_blog_id(), "book_structure", $this->get_book_lenght(pb_get_book_structure()));
        
        if (isset($_SERVER["HTTP_REFERER"])) {
            header("Location: " . $_SERVER["HTTP_REFERER"]);
        }
        
		die();
	}
    
    public function get_book_lenght($book, $include_private = false, $front_back_matter = false){
        $out = Array();

        $out['global'] = Array('parts'     => 0,
                            'chapters'     => 0,
                            'subchapters'  => 0,
                            'words'     => 0,
                            'formulas'  => 0,
                            'h5p'       => 0,
                            'videos'    => 0,
                            'img'       => 0);
        
		$out['id'] = get_current_blog_id();
        $out['timestamp'] = time();
		$out['part'] = Array();


        /*
        if($front_back_matter){
                $parts = array_merge($book['part'],$book['front-matter'],$book['back-matter']);
        }*/


        foreach ($book['part'] as $part) {
            $out['global']['parts'] += 1;
            $chapter_array = Array('part_title' => $part['post_title']);
			$chapter_array['chapter'] = Array();
            
            foreach ($part['chapters'] as $chapter){         
                if(!$include_private && strcmp($chapter['post_status'],'private')==0){
                    continue;
                }
                
                $chapter_index = $out['global']['chapters'];
                
                $chapter_array['chapter'][$chapter_index]['chapter_title'] = $chapter['post_title'];
                $chapter_array['chapter'][$chapter_index]['id'] = $chapter['ID'];
                $chapter_array['chapter'][$chapter_index]['words_until_now'] = $out['global']['words'];
                
                $post = get_post($chapter['ID'])->post_content;
                
                //subchapters ------------------------------------------
                $subchapters = Array();
                $chapter_array['chapter'][$chapter_index]['subchapters'] = 0;
                $chapter_array['chapter'][$chapter_index]['words']     = 0;
                $chapter_array['chapter'][$chapter_index]['h5p']       = 0;
                $chapter_array['chapter'][$chapter_index]['videos']    = 0;
                $chapter_array['chapter'][$chapter_index]['img']       = 0;
                $chapter_array['chapter'][$chapter_index]['formulas']  = 0;
                
                //process html
                $doc = new DOMDocument();
                $doc->loadHTML($post);    
                $selector = new DOMXPath($doc);
                $result = $selector->query('//h1[@class="in-list"]'); //get all h1 elements
                foreach($result as $index=>$node) {
                    $subchapters[$index]['subchapter_title'] = $node->nodeValue;
                    $subchapters[$index]['id'] = $node->getAttribute('id');
                    
                    //get content between subchapters
                    $content = $selector->evaluate('//h1[@class="in-list"]['.($index+1).']/following::text()[count(preceding::h1[@class="in-list"])<='.($index+1).'][not(ancestor::h1)]');
                    
                    $text = '';
                    foreach($content as $val){
                        $text .= $val->nodeValue;
                    }
                    
                    $count_images = $selector->evaluate('count(//h1[@class="in-list"]['.($index+1).']/following::img[count(preceding::h1[@class="in-list"])<='.($index+1).'])');
                    $count_videos = $selector->evaluate('count(//h1[@class="in-list"]['.($index+1).']/following::iframe[count(preceding::h1[@class="in-list"])<='.($index+1).'])');
                    
                    $subchapters[$index]['words']    = str_word_count($text);
                    $subchapters[$index]['h5p']      = substr_count($text,"[h5p");
                    $subchapters[$index]['videos']   = $count_videos;
                    $subchapters[$index]['img']      = $count_images;
                    $subchapters[$index]['formulas'] = substr_count($text,"$$")/2;
                    
                    $chapter_array['chapter'][$chapter_index]['words']     += $subchapters[$index]['words'];
                    $chapter_array['chapter'][$chapter_index]['h5p']       += $subchapters[$index]['h5p'];
                    $chapter_array['chapter'][$chapter_index]['videos']    += $subchapters[$index]['videos'];
                    $chapter_array['chapter'][$chapter_index]['img']       += $subchapters[$index]['img'];
                    $chapter_array['chapter'][$chapter_index]['formulas']  += $subchapters[$index]['formulas'];
                    $chapter_array['chapter'][$chapter_index]['subchapters']  += 1;
                    
                    //$subchapters[$index]['content'] = $text;
                    $chapter_array['chapter'][$chapter_index]['subchapter'] = $subchapters;
                }
                
                if($chapter_array['chapter'][$chapter_index]['subchapters']==0){
                    //improvement: only count words inside div[class = entry-content]
                    
                    $count_images = $selector->evaluate('count(//img)');
                    $count_videos = $selector->evaluate('count(//iframe)');
                    
                    $chapter_array['chapter'][$chapter_index]['words']     =  str_word_count($post);
                    $chapter_array['chapter'][$chapter_index]['h5p']       =  substr_count($post,"[h5p");
                    $chapter_array['chapter'][$chapter_index]['videos']    =  $count_videos;
                    $chapter_array['chapter'][$chapter_index]['img']       =  $count_images;
                    $chapter_array['chapter'][$chapter_index]['formulas']  =  substr_count($post,"$$")/2;                    
                }
                
                $out['global']['words']     += $chapter_array['chapter'][$chapter_index]['words'];
                $out['global']['h5p']       += $chapter_array['chapter'][$chapter_index]['h5p'];
                $out['global']['videos']    += $chapter_array['chapter'][$chapter_index]['img'];
                $out['global']['img']       += $chapter_array['chapter'][$chapter_index]['formulas'];
                $out['global']['formulas']  += $chapter_array['chapter'][$chapter_index]['formulas'];
                $out['global']['subchapters'] += $chapter_array['chapter'][$chapter_index]['subchapters'];
                
				$out['global']['chapters'] += 1;
                //$chapter_array[$chapter_index]['content'] = $post;
            }
            array_push($out['part'],$chapter_array);
        }
        return $out;
    }
}
