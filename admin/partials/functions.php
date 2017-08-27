<?php

$test = "test";

function get_book_lenght($book, $include_private = false, $front_back_matter = false){
    $out = Array();
    
    $out['global'] = Array('parts'     => 0,
                    'chapters'  => 0,
                    'words'     => 0,
                    'formulas'  => 0,
                    'h5p'       => 0,
                    'videos'    => 0,
					'img'       => 0);
    
    
    /*
    if($front_back_matter){
            $parts = array_merge($book['part'],$book['front-matter'],$book['back-matter']);
    }*/
    
    
    foreach ($book['part'] as $part) {
        $out['global']['parts'] += 1;
        $chapter = Array();
        foreach ($part['chapters'] as $chapter){
            if(!$include_private && strcmp($chapter['post_status'],'private')==0){
                return;
            }
            
            //subchapters
            $chapter = eskript_references_for_post($chapter['ID']);
            
        }
        
        array_push($out,$chapter);
    }
    
    return $out;
}



?>