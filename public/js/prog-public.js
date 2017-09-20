(function ($) {
	'use strict';
	//console.log(prog_vars);
    
    

    //only manipulate page when the document is ready
    $(function(){
		//console.log(prog_vars);
        
        //Test if on coverpage
		if(window.location.href.indexOf("chapter") == -1){
			add_continue_btn();
			add_prog_menu();
		}else{
            //add_chapter_length();
        }
        add_bookmark();
        
        try{
        add_progress_circle(prog_vars.lecture_progress,"orange");
        }catch(e){}
        
        try{
		add_progress_circle(prog_vars.bookmark,"green");
        }catch(e){}
        
    });
    
    $(window).load(function () {
        //console.log("Jquery version: "+jQuery.fn.jquery);
        //console.log(prog_vars);
    });

})(jQuery);

var scroll_cutoff = 40;

function add_continue_btn(){
	if(prog_vars.bookmark.length == 0){
		console.log("no bookmark set");
		return;
	}
	
	try {
		if(prog_vars.bookmark.path.length != 0 && ~prog_vars.bookmark.path.indexOf("chapter")){
			$(".call-to-action").append('<a class="btn red" href="'+prog_vars.bookmark.path+"#"+prog_vars.bookmark.subchapter_id+'"><span class="continue-icon"></span>Continue</a>');
		}
	}catch(err) {
    	console.log(err.message);
	}
}

function calculate_words(book,user_mark,lecture_mark){
	
	//if(user_mark == null){return;} //no progress set
	
    var words_till_now = 0;
	var user_words = 0;
	var user_count = false;
	var lecture_words = 0;
	var lecture_count = false;
	var chapters = Array();
    var h5p = Object();
    
    
	
	for(var i=0;i<book.part.length;i++){
        $.each(book.part[i].chapter,function( index, value ) {
            //console.log(value);
            chapters.push(value.words_until_now);
			
			if(value.id == user_mark.chapter_id){
				user_words = value.words_until_now;
				user_count = true;
			}
			
			if(value.id == lecture_mark.chapter_id){
				lecture_words = value.words_until_now;
				lecture_count = true;
			}
			
            words_till_now = value.words_until_now;
            
			if(value.subchapters != 0){
				for(var k=0; k < value.subchapter.length;k++){
					
                    
					
					if(value.subchapter[k].id.localeCompare(user_mark.subchapter_id) == 0){
						user_words = words_till_now;
					}

					if(value.subchapter[k].id.localeCompare(lecture_mark.subchapter_id) == 0){
						lecture_words = words_till_now;
					}
                    
                    words_till_now += value.subchapter[k].words;
                    
                    if(value.subchapter[k].h5p != 0){
                        h5p[words_till_now] = value.subchapter[k].h5p;
                    }
				}
			}
        });
	}
	var out = Object();
	
	out['user_words'] = user_words;
	out['lecture_words'] = lecture_words;
	out['chapters'] = chapters;
    out['h5p'] = h5p;
	
	//console.log(out);
	
	return out;
}

function add_prog_menu(){
    
    if(prog_vars.book_length.length == 0){
        console.warn("book statistic not calculated!");
        return;
    }
    
	var book_data = calculate_words(prog_vars.book_length,prog_vars.bookmark,prog_vars.lecture_progress);
    //console.log(book_data);
	
	var reading_time_approx_h = parseInt(prog_vars.book_length.global.words/(175*60));
	var reading_time_approx_m = parseInt((prog_vars.book_length.global.words/175)%60);
	
	//console.log("user word count: "+book_data['user_words']+" lecture word count: "+book_data['lecture_words']);
	
    $(".third-block-wrap").append('<div class="third-block clearfix"><h2>Learning Progress</h2><p>Reading Time (approx.): '+reading_time_approx_h+'h '+reading_time_approx_m+'min ('+prog_vars.book_length.global.words+' words)</p><p>Additional material:  '+prog_vars.book_length.global.h5p+' interactivity modules, '+prog_vars.book_length.global.videos+' videos, '+prog_vars.book_length.global.img+' images, '+prog_vars.book_length.global.formulas+' formulas</p><div class="progressBarContainer"><div class="overflow_hidden"><div class="progressBar orange"></div><div class="progressBar green"></div></div></div></div><p></p>');
	
	var container_width = $(".progressBarContainer").width();
	var lastlabel = -100;
	book_data['chapters'].forEach(function(element,index) {
		if(index != 0){
			var label = (index+1);
			var pos = element/(prog_vars.book_length.global.words);
			if((pos-lastlabel)*container_width < 20){
				label = "";
			}else{
				lastlabel = pos;
			}
			
			$(".progressBarContainer").append('<div class="progressBar marker" style="transform: translateX('+String((pos*100)-100)+'%)"><div class="label">'+label+'</div></div>');
		}
	});
    
    $.each(book_data['h5p'], function(key,value) {
        pos = key/(prog_vars.book_length.global.words);
        $(".progressBarContainer").append('<div class="progressBar marker no_markup" style="transform: translateX('+String((pos*100)-100)+'%)"><div class="circle blue"></div></div>');
	});
	
	
	var user_prog = book_data['user_words']/(prog_vars.book_length.global.words);
	var lecture_prog = book_data['lecture_words']/(prog_vars.book_length.global.words);
	$(".progressBar.green").css('transform','translateX('+String((user_prog*100)-100)+'%)');
	$(".progressBar.orange").css('transform','translateX('+String((lecture_prog*100)-100)+'%)');
}

function add_bookmark(){
	if(window.location.href.indexOf("chapter") > -1){
		$(".a11y-toolbar ul").append('<li><a href="javascript:save_bookmark();" role="button" id="save_bookmark" title="set Bookmark"><span class="dashicons dashicons-book"></span></a></li>');

		if(prog_vars.admin == 1){
			$(".a11y-toolbar ul").append('<li><a href="javascript:save_lecture_progress();" role="button" style="background-color:rgb(255, 187, 0);" id="save_lecture_progress" title="lecture progress"><span class="dashicons dashicons-book"></span></a></li>');
		}
	}
}

function add_progress_circle(mark,color){
    if(mark == null){return;} //no progress set
    
	if(typeof mark.subchapter_id != 'undefined' && mark.subchapter_id.length != 0){
		$('#'+mark.subchapter_id).append("<div class='prog_circle "+color+"'></div>").css('position','relative');
		if($('.section a[ href*="'+mark.subchapter_id+'"]').append("<div class='prog_circle "+color+"'></div>").css('position','relative').length > 0){
			//console.log("submenu found");
			return;	
		}
	}
	
	$('.chapter a[href="'+mark.path+'"]').append("<div class='prog_circle "+color+"'></div>").css('position','relative');
}

function calculate_subchapter_length(chapter){
    var out = Object();
    if(chapter.subchapters == 0){
        console.log("no subchapters");
        return out;
    }
    
    $.each(chapter.subchapter,function(index,subchapter){
        out[subchapter.id] = subchapter.words;
    });
    
    return out;
}

function add_chapter_length(){
    var current_chapter;
    //get current chapter
    for(var i=0;i<prog_vars.book_length.part.length;i++){
        $.each(prog_vars.book_length.part[i].chapter,function( index, value ) {
            if(value.id==prog_vars.chapter_id){
                current_chapter = value;
            }
        });
    }
    
    var subchapter_length = calculate_subchapter_length(current_chapter);
    console.log(subchapter_length);
    
    var max_length = 0;
    $.each(subchapter_length, function (key, val) {
        if(max_length < val){
            max_length = val;
        }
    });
    
    $.each(subchapter_length, function (key, val) {
        var l = (val/max_length)*100-100;
        $('.section a[ href*="'+key+'"]').parent().append('<div class="progressBarContainer noBorder small"><div class="overflow_hidden"><div class="progressBar blue" style="transform:translateX('+String(l)+'%)"></div></div></div>');
    });

}

function save_bookmark(){
	var cutoff = $(window).scrollTop();
	var subchapter_id = "";
	$('.entry-content').find( "h1" ).each(function(){
		if($(this).offset().top - scroll_cutoff > cutoff){
			return false; // stops the iteration after the first one on screen
		}
		subchapter_id = $(this).prop('id');
	});
    
    var data = {
        'action': 'prog_save_bookmark',
        'progNonce' : prog_vars.ajax_nonce,
		'book_id' : prog_vars.book_id,
		'chapter_id' :prog_vars.chapter_id,
		'subchapter_id': subchapter_id,
        'path': prog_vars.path
    };
    
    console.log(data);
    
    jQuery.post(prog_vars.ajax_url, data, function(response) {
		console.log('Got this from the server: ' + response);
		if(response >= 1){
			//TODO better animation
			$( "#save_bookmark" ).css('background-color', 'rgb(78, 175, 82)');
			
			//reset status point
			$('.prog_circle.green').remove();
            prog_vars.bookmark = Array();
			prog_vars.bookmark.subchapter_id = subchapter_id;
			add_progress_circle(prog_vars.bookmark,"green");
		}
	});
}

function save_lecture_progress(){
	var cutoff = $(window).scrollTop();
	var subchapter_id = "";
	$('.entry-content').find( "h1" ).each(function(){
		if($(this).offset().top - scroll_cutoff> cutoff){
			return false;
		}
		subchapter_id = $(this).prop('id');
	});
    
    var data = {
        'action': 'prog_save_lecture_progress',
        'progNonce' : prog_vars.ajax_nonce,
		'chapter_id': prog_vars.chapter_id,
		'subchapter_id': subchapter_id,
        'path': prog_vars.path
    };
    
    console.log(data);
    
    jQuery.post(prog_vars.ajax_url, data, function(response) {
		console.log('Got this from the server: ' + response);
		if(response >= 1){
			//reset status point
			$('.prog_circle.orange').remove();
            prog_vars.lecture_progress = Array();
			prog_vars.lecture_progress.subchapter_id = subchapter_id;
			add_progress_circle(prog_vars.lecture_progress,"orange");
		}
	});
}