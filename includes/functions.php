<?php

function get_book_lenght($book, $include_private = false, $front_back_matter = false){
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
            
            if(strlen($post) <= 1){
                $out['global']['chapters'] += 1;
                continue;
            }
            

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
            $out['global']['videos']    += $chapter_array['chapter'][$chapter_index]['videos'];
            $out['global']['img']       += $chapter_array['chapter'][$chapter_index]['img'];
            $out['global']['formulas']  += $chapter_array['chapter'][$chapter_index]['formulas'];
            $out['global']['subchapters'] += $chapter_array['chapter'][$chapter_index]['subchapters'];

            $out['global']['chapters'] += 1;
            //$chapter_array[$chapter_index]['content'] = $post;
        }
        array_push($out['part'],$chapter_array);
    }
    return $out;
}

//maby replace xPath for this
function find_html_tag($string,$tag,$searchposition){
    $out = Array('content' => '','startposition' => $searchposition, 'endposition' => -1);
    
    //TODO
    $start_tag_end = 0;
    $end_tag_start = 0;
    
    $out['startposition'] = strpos($string,"<".$tag,$searchposition);
    
    $brackets_counter = 0;
    for($i = $out['startposition']+1;$i<strlen($string);$i++){
        if(substr($string, $i, 1) == '<' && substr($string, $i, 2) != "</"){
            $brackets_counter += 1;
        }
        
        if(substr($string, $i, 2) == "</"){
            $brackets_counter -= 1;
            echo $brackets_counter;
        }
        
        if($brackets_counter == 0 && substr($string, $i, strlen($tag)) == "</".$tag){
            $end_tag_start = $i;
            $out['endposition'] = strpos($string,">",$i);
        }
    }
    
    $start_tag_end = strpos($string,'>',$out['startposition']);
    $out['content'] = substr($string,$start_tag_end,$end_tag_start);
    return $out;
}

function recalculate_stats($book){
    echo update_blog_option(get_current_blog_id(), "book_structure", get_book_lenght(pb_get_book_structure()));
}

function delete_data(){
        //echo "<script>console.log(" . json_encode($this->get_book_lenght(pb_get_book_structure())).")</script>";
        
        //TODO security noonce
    
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
        
        if (isset($_SERVER["HTTP_REFERER"])) {
            header("Location: " . $_SERVER["HTTP_REFERER"]);
        }
        
		die();
}
?>