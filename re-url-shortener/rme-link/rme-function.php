<?php
	
	$GLOBALS['var'] = $_POST['url'];
	

	
	function do_scraper($var){
	
		$handle = @fopen($GLOBALS['var'],'r');
		if($handle !== false){
			$html = file_get_contents($GLOBALS['var']);
		}
		else{
		   echo 'invalid';
		   exit;
		}
		
		$doc = new DOMDocument();
		@$doc->loadHTML($html);
		$ako = $doc->getElementsByTagName('title');

		foreach( $doc->getElementsByTagName('meta') as $meta ) {
			if ( strpos($meta->getAttribute('property'), 'description') !== FALSE ) 
				$arr['description'] = $meta->getAttribute('content');

			if ( strpos($meta->getAttribute('property'), 'title') !== FALSE )
				$arr['site_title'] = $meta->getAttribute('content');

			if ( strpos($meta->getAttribute('property'), 'url') !== FALSE )
				$arr['url'] =$meta->getAttribute('content');

			if ( strpos($meta->getAttribute('property'), 'image') !== FALSE )
				$arr['image'] = $meta->getAttribute('content');
	
			if ( strpos($meta->getAttribute('property'), 'og:type') !== FALSE )
				$arr['type'] = $meta->getAttribute('content');

			//MUSIC
			if ( strpos($meta->getAttribute('property'), 'music:musician') !== FALSE )
				$arr['musician'] = $meta->getAttribute('content');

			if ( strpos($meta->getAttribute('property'), 'music:song') !== FALSE )
				$arr['song'] = $meta->getAttribute('content');

			if ( strpos($meta->getAttribute('property'), 'music:album') !== FALSE )
				$arr['album'] = $meta->getAttribute('content');

			if ( strpos($meta->getAttribute('property'), 'music:release_date') !== FALSE )
				$arr['release_date'] = $meta->getAttribute('content');
				
			//VIDEO
			if ( strpos($meta->getAttribute('property'), 'video:url') !== FALSE )
				$arr['video_url'] = $meta->getAttribute('content');

			if ( strpos($meta->getAttribute('property'), 'video:secure_url') !== FALSE )
				$arr['video_secure_url'] = $meta->getAttribute('content');

			if ( strpos($meta->getAttribute('property'), 'video:type') !== FALSE )
				$arr['video_type'] = $meta->getAttribute('content');

			if ( strpos($meta->getAttribute('property'), 'video:width') !== FALSE )
				$arr['video_width'] = $meta->getAttribute('content');

			if ( strpos($meta->getAttribute('property'), 'video:height') !== FALSE )
				$arr['video_height'] = $meta->getAttribute('content');

			//ARTICLE
			if ( strpos($meta->getAttribute('property'), 'article:published_time') !== FALSE )
				$arr['published_time'] = $meta->getAttribute('content');

			if ( strpos($meta->getAttribute('property'), 'article:modified_time') !== FALSE )
				$arr['modified_time'] = $meta->getAttribute('content');	

			if ( strpos($meta->getAttribute('property'), 'article:expiration_time') !== FALSE )
				$arr['exp_time'] = $meta->getAttribute('content');	

			if ( strpos($meta->getAttribute('property'), 'article:author') !== FALSE )
				$arr['article_author'] = $meta->getAttribute('content');

			if ( strpos($meta->getAttribute('property'), 'article:section') !== FALSE )
				$arr['article_section'] = $meta->getAttribute('content');		

			//BOOK
			if ( strpos($meta->getAttribute('property'), 'book:author') !== FALSE )
				$arr['book_author'] = $meta->getAttribute('content');

			if ( strpos($meta->getAttribute('property'), 'book:isbn') !== FALSE )
				$arr['book_isbn'] = $meta->getAttribute('content');	
          
			if ( strpos($meta->getAttribute('property'), 'book:release_date') !== FALSE )
				$arr['book_release_date'] = $meta->getAttribute('content');	

			//PROFILE
			if ( strpos($meta->getAttribute('property'), 'profile:first_name') !== FALSE )
				$arr['profile_first_name'] = $meta->getAttribute('content');
				
			if ( strpos($meta->getAttribute('property'), 'profile:last_name') !== FALSE )
				$arr['profile_last_name'] = $meta->getAttribute('content');
				
			if ( strpos($meta->getAttribute('property'), 'profile:username') !== FALSE )
				$arr['profile_username'] = $meta->getAttribute('content');

			if ( strpos($meta->getAttribute('property'), 'profile:gender') !== FALSE )
				$arr['profile_gender'] = $meta->getAttribute('content');

			//----
			if ( strpos($meta->getAttribute('name'), 'description') !== FALSE )
				$arr['description'] = $meta->getAttribute('content');
		
			if ( strpos($meta->getAttribute('name'), 'title') !== FALSE )
				$arr['site_title'] = $meta->getAttribute('content');

			if ( strpos($meta->getAttribute('name'), 'url') !== FALSE )
				$arr['url'] =$meta->getAttribute('content');

			if ( strpos($meta->getAttribute('name'), 'image') !== FALSE )
				$arr['image'] = $meta->getAttribute('content');

			}
			 $arr['title'] =  $ako->item(0)->nodeValue;
	
		echo json_encode($arr);
	}
	
	