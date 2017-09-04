<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://www.ee.ethz.ch/de/departement/personen-a-bis-z/person-detail.html?persid=208843
 * @since      0.0.2
 *
 * @package    Prog
 * @subpackage Prog/admin/partials
 */

?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="wrap">

    <h2><?php echo esc_html(get_admin_page_title()); ?></h2>
    
    <p>This plugin is in development, settings might or might not work</p>
    
    <p><?php
        $teststring = "asdf<h1>TEST TEST<p>...</p></h1>qwetpoiuxyv";
        echo "<br>".$teststring;
        var_dump(find_html_tag($teststring,"h1",1));
    ?></p>
    
    <form method="post" name="cleanup_options" action="options.php">
        
        <?php
            //Grab all options
            $options = get_option($this->plugin_name);

            $lecture_progress = $options['lecture_progress'];
            $debug = $options['debug'];
        
            //$debug = 1;
        
            //echo "<script>console.log( 'lecture_progress: " .$lecture_progress . ",debug: " .$debug . "' );</script>";
        ?>

        <?php
            settings_fields($this->plugin_name);
            do_settings_sections($this->plugin_name);
        ?>
        
        <?php settings_fields($this->plugin_name); ?>
    
        <fieldset>
            <legend class="screen-reader-text"><span>Show lecture progress</span></legend>
            <label for="<?php echo $this->plugin_name; ?>-lecture_progress">
                <input type="checkbox" id="<?php echo $this->plugin_name; ?>-lecture_progress" name="<?php echo $this->plugin_name; ?> [lecture_progress]" value="1" <?php checked($lecture_progress, 1); ?>/>
                <span><?php esc_attr_e('Show lecture progress', $this->plugin_name); ?></span>
            </label>
        </fieldset>
        
        <fieldset>
            <legend class="screen-reader-text"><span>Display chapter length</span></legend>
            <label for="<?php echo $this->plugin_name; ?>-lchapter_length">
                <input type="checkbox" id="<?php echo $this->plugin_name; ?>-chapter_length" name="<?php echo $this->plugin_name; ?> [chapter_length]" value="1" <?php checked($lecture_progress, 1); ?>/>
                <span><?php esc_attr_e('Display chapter length', $this->plugin_name); ?></span>
            </label>
        </fieldset>
        
        <fieldset>
            <legend class="screen-reader-text"><span>debug</span></legend>
            <label for="<?php echo $this->plugin_name; ?>-debug">
                <input type="checkbox" id="<?php echo $this->plugin_name; ?>-debug" name="<?php echo $this->plugin_name; ?> [debug]" value="1" <?php checked($debug, 1); ?>/>
                <span><?php esc_attr_e('debug', $this->plugin_name); ?></span>
            </label>
        </fieldset>
        
    <?php submit_button('Save all changes', 'primary','submit', TRUE); ?>
    </form>
    
    <form action="<?php echo admin_url( 'admin-post.php' ); ?>" method="post">
        <input type="hidden" name="action" value="delete_data">
        <input type="hidden" name="_wp_http_referer" value="<?php echo $redirect; ?>">
        <?php submit_button('Delete all user Data'); ?>
    </form>
    
    <form action="<?php echo admin_url( 'admin-post.php' ); ?>" method="post">
        <input type="hidden" name="action" value="get_book_structure">
        <input type="hidden" name="_wp_http_referer" value="<?php echo $redirect; ?>">
        <?php submit_button('Get Book Stucture'); ?>
    </form>
	
    <h3>Book Statistics</h3>
    
    <?php 
    
        $book_structure = get_blog_option(get_current_blog_id(), "book_structure");
        if(!$book_structure){
            $book_structure = Array('timestamp' => 0);
            $book_structure['global'] =   Array('parts'     => 0,
                        'chapters'         => 0,
                        'subchapters'      => 0,
                        'words'     => 0,
                        'formulas'  => 0,
                        'h5p'       => 0,
                        'videos'    => 0,
                        'img'       => 0);
        }
    ?>
    
    <script>
        console.log(<?php  echo json_encode($book_structure); ?>);
    </script>
    
	<form action="<?php echo admin_url( 'admin-post.php' ); ?>" method="post">
        <p>Parts: <?php echo $book_structure['global']['parts']?></p>
        <p>Chapters: <?php echo $book_structure['global']['chapters']?></p>
        <p>Subchapters: <?php echo $book_structure['global']['subchapters']?></p>
        <p>Words: <?php echo $book_structure['global']['words']?></p>
        <p>Formulas: <?php echo $book_structure['global']['formulas']?></p>
        <p>h5p: <?php echo $book_structure['global']['h5p']?></p>
        <p>Images: <?php echo $book_structure['global']['img']?></p>
        <p>Videos: <?php echo $book_structure['global']['videos']?></p>
        timestamp: <?php echo $book_structure['timestamp']?>
		<p><b>more infos in the console</b></p>
        
		<input type="hidden" name="action" value="recalculate_statistics">
        <input type="hidden" name="_wp_http_referer" value="<?php echo $redirect; ?>">
		<?php submit_button('Recalculate'); ?>
	</form>

</div>
