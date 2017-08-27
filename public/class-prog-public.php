<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.ee.ethz.ch/de/departement/personen-a-bis-z/person-detail.html?persid=208843
 * @since      0.0.2
 *
 * @package    Prog
 * @subpackage Prog/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Prog
 * @subpackage Prog/public
 * @author     Lorin Muehlebach <mlorin@ethz.ch>
 */
class Prog_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    0.0.2
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    0.0.2
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    0.0.2
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    0.0.2
	 */
	public function enqueue_styles() {
		/**
		 * An instance of this class should be passed to the run() function
		 * defined in Prog_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Prog_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/prog-public.css', array(), $this->version, 'all' );
	}

    
	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    0.0.2
	 */
	public function enqueue_scripts() {

		/**
		 * An instance of this class should be passed to the run() function
		 * defined in Prog_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Prog_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */ 
        wp_register_script($this->plugin_name."js", plugin_dir_url( __FILE__ ) . "js/prog-public.js" );
        wp_enqueue_script($this->plugin_name."js");
        wp_localize_script($this->plugin_name."js", "php_vars", $this->load_data());
    }
    
    public function load_data(){
        $book_structure = get_blog_option(get_current_blog_id(), "book_structure");
        if(!$book_structure){
            //TODO no book structure calculated
        }
        
        $lecture_progress = get_blog_option(get_current_blog_id(), "lecture_progress"); //false if no lectureprogress exists
                
        $bookmarks = get_user_meta(get_current_user_id(),'prog_bookmark',true);
        $user_progress = "";
        if($bookmarks && array_key_exists(get_current_blog_id(), $bookmarks)){
            $user_progress = $bookmarks[get_current_blog_id()];
        }
        
        $ref = eskript_reference_for_id(get_the_ID()); //TODO independent of e-script
        $href = get_permalink($ref['post']);
		
        
        $array = array(
	    "admin" => current_user_can( 'manage_options' ),
        "ajax_url" => admin_url( 'admin-ajax.php' ),
        "ajax_nonce" => wp_create_nonce( "progNonce" ),
        "bookmark" => $user_progress,
		"book_id" => get_current_blog_id(),
		"chapter_id" => get_the_ID(),
        "path" => $href,
		"book_length" => $book_structure,
		"lecture_progress" => $lecture_progress,
		"debug" => get_the_ID()
	    );
		
		return $array;
    }
    
    //TODO for what is this function?
    public function initialize(){
        //add_post_meta($post->ID, 'my-post-information', 'This is my favorite post.' );
    }
    
    /*
    *Ajax callback function for saving user bookmarks
    */
    public function prog_save_bookmark() {
        if ( !isset($_POST['progNonce']) || !wp_verify_nonce( $_POST['progNonce'], 'progNonce' ) ){die ( 'you cant do this' );}
				
		$bookmarks = get_user_meta(get_current_user_id(),'prog_bookmark',true);
        
        if(!is_array($bookmarks)){
            $bookmarks = Array();
        }
		
		$bookmarks[$_POST['book_id']] = Array( "chapter_id" => $_POST['chapter_id'], "subchapter_id" => $_POST['subchapter_id'],"path" => $_POST['path']);
		
		echo update_user_meta( get_current_user_id(), 'prog_bookmark', $bookmarks);
        
        //delete_user_meta( get_current_user_id(), 'prog_bookmark');
        
	    wp_die(); // this is required to terminate immediately and return a proper response
    }
	
	/*
	*Ajax callback function for lecture progress only for admins
	*/
	public function prog_save_lecture_progress(){
		if ( !isset($_POST['progNonce']) || !wp_verify_nonce( $_POST['progNonce'], 'progNonce' ) ){die ( 'you cant do this' );}
		//TODO test if admin
		
		$data = Array("chapter_id" => $_POST['chapter_id'],
					  "subchapter_id" => $_POST['subchapter_id'],
					  "path" => $_POST['path']
					 );
		
		echo update_blog_option(get_current_blog_id(), "lecture_progress", $data);
		
		//var_dump(get_blog_option(get_current_blog_id(), "lecture_progress"));
		
		wp_die(); // this is required to terminate immediately and return a proper response
	}
	
    
	/*
     *Function to calculate the lenght of all chapters in a book
     *input Press Book book structure
    */
    
    public function book_length($book,$user_prog = null,$lecture_prog = null){
        $out = Array();
		$global = Array('charakter' => 0,
					   	'words' => 0	,
					   	'h5p' 	=> 0	,
                        'videos'=> 0    ,
					   	'img'	=> 0);
		
        foreach ($book['part'] as $part) {
            $title = pb_strip_br( $part['post_title'] );
            $out[$title] = Array();
            foreach($part['chapters'] as $chapter){
				$chapter_data = $this->content_length($chapter['ID'],$user_prog,$lecture_prog);
				
				$global['charakter'] += $chapter_data['charakter'];
				$global['words'] += $chapter_data['words'];
				$global['h5p'] += $chapter_data['h5p'];
                $global['videos'] += $chapter_data['videos'];
				$global['img'] += $chapter_data['img'];
				
				if($user_prog != null && $chapter_data['user_prog']){
					$global['user_prog'] = $global['words'];
				}
				
				if($lecture_prog != null && $chapter_data['lecture_prog']){
					$global['lecture_prog'] = $global['words'];
				}
				
                $out[$title][$chapter['post_name']]= $chapter_data;
            }
        }
		
		$out["global"] = $global;
		return $out;
    }
    
	
	public function content_length($chapter,$user_prog = null,$lecture_prog = null){
        $post = get_post($chapter);
		$content = $post->post_content;
		
		$out = Array();
		$out['charakter'] = strlen(wp_strip_all_tags($content));
		$out['words'] = str_word_count(wp_strip_all_tags($content));
		$out['h5p'] = substr_count($content,"[h5p");
        $out['videos'] = substr_count($content,"</iframe>");
		$out['img'] = substr_count($content,"img");
		
		if($user_prog != null){
			$out['user_prog'] = $user_prog['post'] == $chapter;
		}
		
		if($lecture_prog != null){
			$out['lecture_prog'] = $lecture_prog['post'] == $chapter;
		}
		
		return $out;
    }
    

}
