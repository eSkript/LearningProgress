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
