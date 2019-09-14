<?php

	

	global $rme_edit;


	if($_GET['rme_edit']!=""){
	
		echo '<script type="text/javascript">';
			echo 'jQuery(document).ready(function(){';
				echo 'jQuery("#add").click(function(){  ';
					echo '	jQuery("#addrme")[0].reset();';
				echo '});';
			echo '});';
		echo '</script>';
	
	}
?>

<div id="rme-wrapper">
<div class="loading" style="text-align:center;"><h3>Processing...</h3><img src="/wp-content/plugins/url-shortener/admin/inc/images/spinner_128_anti_alias.gif" width="50"></div>
	<div class="rmeheader">
		<h2 id="rme-heading">RME Shortener
		<span class="rme-btn" id="toscrape">Scrape</span> <span class="rme-btn" id="add">Add New</span> <span class="rme-btn" id="data">Entries</span>
		</h2>
	</div>

	<div class="clear"></div>

	<div class="rme-block-container rmeadd">
		<div id="loading"></div>
		<div class="rme-form">
			<form method="POST" action="admin.php?page=rme_shortener" name="addurl">
				<p class="rme-note" id="inst">Note : Add this shortcode to anywhere to display the front-end form <strong>[rme-url-shortener-front]</strong></p>
				<div id="scrapeurl">
					<input type="text" name="url" id="txturl" value="<?php if($_POST['url']==""){}else{ echo $rme_rows[0]->urlurl; echo $scraped_url; echo $_POST['urls']; };?>" placeholder="Enter URL Here">
				</div>
				<div>
					<input id="btnscrape" class="rme-btn fbtn" name="rmesubmit" type="submit" value="<?php echo 'Scrape'; ?>">
					<div class="clear"></div>
				</div>
			</form>
		</div>
	</div>
	<div class="rme-block-container" id="scrdata">
		<form method="POST" action="" id="addrme">
		<div class="tblheader">
			<h4>Data scraped from URL <input id="btnconvert" class="rme-btn fbtn" name="rmesubmit" type="submit" value="Save"></h4> 
		</div>
		<table class="scrape">				
			<tbody>
			<?php //OG Title ?>
				<tr class="base">
					<td class="tle"><label <?php //for="txttitle" ?> >Title:</label></td>
					<td contenteditable='true' id="scrape_title"><?php echo $rme_rows[0]->urltitle; echo $scraped_title;  echo $resultsquery[0]->urlsitename;?></td>
				</tr>
			<?php //OG Image ?>
				<tr class="base alt">
					<td>Upload Image</td>
					<td><label for="img">
						<input id="upload_image" type="text" size="36" name="scrape_image" value="<?php echo $rme_rows[0]->urlimg; echo $scraped_image; echo $resultsquery[0]->urlimg;?>" />
						<input id="upload_image_button" type="button" value="Upload Image" />
						<br />Enter an URL or upload an image for the banner.
						</label>
					</td>
				<!-- <td class="tle"><label for="img">Image: </label></td>

					// <td><a class="uploadimg" href="#upload"><img contenteditable='true' id="scrape_image" src="<?php echo $rme_rows[0]->urlimg; echo $scraped_image; echo $resultsquery[0]->urlimg;?>" alt="" /></a></td>		
					 -->
				</tr>
			<?php //Site URL ?>
				<tr class="base">
					<td class="tle"><label for="img">URL: </label></td>
					<td contenteditable='true' id="scrape_url"><?php if($_POST['url']==""){ echo $resultsquery[0]->urlurl; }else{ echo $rme_rows[0]->urlurl; echo $scraped_url; };?></td>
				</tr>
			<?php //OG Description ?>
				<tr class="base alt">
					<td class="tle"><label <?php //for="txtdesc"?> >Description: </label></td>
					<td>
					<!-- <input type="text" name="description" id="txtdesc" value="<?php //echo $rme_rows[0]->urldesc;  echo $scraped_desc;?>">-->
					<div contenteditable='true' id="scrape_desc" >
						<?php echo $rme_rows[0]->urldesc;  echo $scraped_desc; echo $resultsquery[0]->urldesc;?>
					</div>
					</td>							
				</tr>
			<?php //OG Shorten ?>
				<tr class="base">
					<td class="tle"><label <?php //for="txtdesc"?> >Shorten Url: </label></td>
					<td <?php if( $GLOBALS['rme_edit'] !=""){} else { echo "contenteditable='true'"; } ?> id="scrape_shorten"><?php echo $rme_rows[0]->urlshorten; echo $resultsquery[0]->urlshorten;?></td>							
				</tr>
			<?php //OG Shorten ?>
				<tr class="base alt">
					<td class="tle"><label <?php //for="txtdesc"?> >Redirecting Pixel: </label></td>
					<td><div contenteditable='true' id="scrape_red_pixel"><?php echo stripslashes(htmlspecialchars($rme_rows[0]->urlredpixel)); echo stripslashes(htmlspecialchars($resultsquery[0]->urlredpixel));?></div></td>
				</tr>				
			<?php //REDIRECT TYPE ?>
				<tr class="base">
					<td class="tle"><label for="urlredirect">Redirect: </label></td>
					<td>
						<select name="urlredirect" id="urlredirect" value="<?php echo $rme_rows[0]->urlredirect;  echo $resultsquery[0]->urlredirect;?>?>">
							<option value="">----</option>
							<option value="301">301</option>
							<option value="307">307</option>
						</select>
					</td>
				</tr>
			<?php //OG Type ?>
				<tr class="base alt">
					<td class="tle"><label for="rmeogtype">Type: </label></td>
					<td>
						<select name="rmeogtype" id="rmeogtype" value="<?php echo $scraped_type;?>">
							<option value="">----</option>
							<option <?php if ($scraped_type == '' || $resultsquery[0]->urltype == "") echo 'selected'; ?> value="website">Site</option>
							<option <?php if ($scraped_type == 'music' || $resultsquery[0]->urltype == "music") echo 'selected'; ?> value="music">Music</option>
							<option <?php if ($scraped_type == 'video' || $resultsquery[0]->urltype == "video") echo 'selected'; ?> value="video">Video</option>
							<option <?php if ($scraped_type == 'article' || $resultsquery[0]->urltype == "article") echo 'selected'; ?> value="article">Article</option>
							<option <?php if ($scraped_type == 'book' || $resultsquery[0]->urltype == "book") echo 'selected'; ?> value="book">Book</option>
							<option <?php if ($scraped_type == 'profile' || $resultsquery[0]->urltype == "profile") echo 'selected'; ?> value="profile">Profile</option>
						</select>
					</td>							
				</tr>
			</tbody>
		</table>
	</div>
	<div class="rme-block-container">
		<div class="attrib_holder">		
			<table class="scrape" id="article">
				<tbody>
				<?php //Published Time ?>
					<tr class="base">
						<td class="tle"><label>Published Time: </label></td>
						<td contenteditable='true' id="article_published_time"><?php if( $GLOBALS['rme_edit'] !=""){echo $resultsquery[0]->aPubTime;}else{ if($scraped_article_published_time == ""){ echo "Place something";} else { echo $scraped_article_published_time; }}?></td>							
					</tr>				
				<?php //Modified Time ?>	
					<tr class="base alt">
						<td class="tle"><label>Modified Time: </label></td>
						<td contenteditable='true' id="article_modified_time"><?php if( $GLOBALS['rme_edit'] !=""){ echo $resultsquery[0]->aModTime; }else{if($scraped_article_modified_time == ""){ echo "Place something";} else { echo $scraped_article_modified_time; }}?></td>
					</tr>
				<?php //Expiration Time ?>		
					<tr class="base">
						<td class="tle"><label>Expiration Time: </label></td>
						<td contenteditable='true' id="article_exp_time"><?php if( $GLOBALS['rme_edit'] !=""){ echo $resultsquery[0]->aExpTime; }else{if($scraped_article_exp_time == ""){ echo "Place something";} else { echo $scraped_article_exp_time; } }?></td>
					</tr>	
				<?php //Author ?>		
					<tr class="base alt">
						<td class="tle"><label>Author: </label></td>
						<td contenteditable='true' id="article_author"><?php if( $GLOBALS['rme_edit'] !=""){ echo $resultsquery[0]->aAuthor; }else{ if($scraped_article_author == ""){ echo "Place something";} else { echo $scraped_article_author; }}?></td>						
					</tr>	
				<?php //Section ?>		
					<tr class="base">
						<td class="tle"><label>Section: </label></td>
						<td contenteditable='true' id="article_section"><?php if( $GLOBALS['rme_edit'] !=""){ echo $resultsquery[0]->aAuthor; } else { if($scraped_article_section == ""){ echo "Place something";} else { echo $scraped_article_section; }}?></td>
					</tr>				
				</tbody>
			</table>

			<table class="scrape" id="book">
				<tbody>
				<?php //Author ?>
					<tr class="base">
						<td class="tle"><label>Author: </label></td>
						<td contenteditable='true' id="book_author"><?php if( $GLOBALS['rme_edit'] !=""){ echo $resultsquery[0]->bAuthor; } else { if($scraped_book_author == ""){ echo "Place something";} else { echo $scraped_book_author; } }?></td>
					</tr>
				<?php //ISBN ?>	
					<tr class="base alt">
						<td class="tle"><label>ISBN: </label></td>
						<td contenteditable='true' id="book_isbn"><?php
						if( $GLOBALS['rme_edit'] !=""){ echo $resultsquery[0]->bISBN; } else { if($scraped_book_isbn == ""){ echo "Place something"; } else { echo $scraped_book_isbn; } } ?></td>
					</tr>				
				<?php //Release Date ?>		
					<tr class="base">
						<td class="tle"><label>Release Date: </label></td>
						<td contenteditable='true' id="book_release_date"><?php
						if( $GLOBALS['rme_edit'] !=""){ echo $resultsquery[0]->bRelease; } else { if($scraped_book_release == ""){ echo "Place something"; } else { echo $scraped_book_release_date; } } ?></td>
					</tr>				
				</tbody>
			</table>

			<table class="scrape" id="profile">
				<tbody>			
				<?php //First Name ?>
					<tr class="base">
						<td class="tle"><label>First Name: </label></td>
						<td contenteditable='true' id="profile_first_name"><?php if( $GLOBALS['rme_edit'] != ""){ echo $resultsquery[0]->pFName;} else { if($scraped_profile_first_name == ""){ echo "Place something";} else { echo $scraped_profile_first_name; }  }?></td>
					</tr>
				<?php //Last Name ?>	
					<tr class="base alt">
						<td class="tle"><label>Last Name: </label></td>
						<td contenteditable='true' id="profile_last_name"><?php if( $GLOBALS['rme_edit'] != ""){ echo $resultsquery[0]->pLName;} else { if($scraped_profile_last_name == ""){ echo "Place something";} else { echo $scraped_profile_last_name; } } ?></td>
					</tr>				
				<?php //Username ?>		
					<tr class="base">
						<td class="tle"><label>Username: </label></td>
						<td contenteditable='true' id="profile_username"><?php if( $GLOBALS['rme_edit'] !=""){ echo $resultsquery[0]->pUsername;} else { if($scraped_profile_username == ""){ echo "Place something";} else { echo $scraped_profile_username; } } ?></td>
					</tr>
				<?php //Gender ?>		
					<tr class="base alt">
						<td class="tle"><label>Gender: </label></td>
						<td contenteditable='true' id="profile_gender"><?php if( $GLOBALS['rme_edit'] !=""){ echo $resultsquery[0]->pGender;} else { if($scraped_profile_gender == ""){ echo "Place something";} else { echo $scraped_profile_gender; } } ?></td>
					</tr>
				</tbody>
			</table>

			<table class="scrape" id="video">
				<tbody>				
				<?php //Video Url ?>
					<tr class="base">
						<td class="tle"><label>Video Url:</label></td>
						<td contenteditable='true' id="video_url"><?php if( $GLOBALS['rme_edit'] !=""){ echo $resultsquery[0]->vUrl; } else { if($scraped_video_url == ""){ echo "Place something";} else { echo $scraped_video_url; } } ?></td>
					</tr>								
				<?php //Secure Url ?>
					<tr class="base alt">
						<td class="tle"><label>Video Secure Url:</label></td>
						<td contenteditable='true' id="video_secure_url"><?php if( $GLOBALS['rme_edit'] !=""){ echo $resultsquery[0]->vSUrl; } else { if($scraped_video_secure_url == ""){ echo "Place something";} else { echo $scraped_video_secure_url; } } ?></td>
					</tr>								
				<?php //Video Type ?>
					<tr class="base">
						<td class="tle"><label>Video Type:</label></td>
						<td contenteditable='true' id="video_video_type"><?php if( $GLOBALS['rme_edit'] !=""){ echo $resultsquery[0]->vType; } else { if($scraped_video_type == ""){ echo "Place something";} else { echo $scraped_video_type; }   }?></td>
					</tr>					
				<?php //Video Width ?>
					<tr class="base alt">
						<td class="tle"><label>Video Width:</label></td>
						<td contenteditable='true' id="video_video_width"><?php if( $GLOBALS['rme_edit'] !=""){ echo $resultsquery[0]->vWidth; } else { if($scraped_video_width == ""){ echo "Place something";} else { echo $scraped_video_width; } }?></td>
					</tr>				
				<?php //Video Height ?>
					<tr class="base">
						<td class="tle"><label>Video Height:</label></td>
						<td contenteditable='true' id="video_video_height"><?php if( $GLOBALS['rme_edit'] !=""){ echo $resultsquery[0]->vHeight; } else { if($scraped_video_height == ""){ echo "Place something";} else { echo $scraped_video_height; }  }?></td>
					</tr>								
				</tbody>
			</table>

			<table class="scrape" id="music">
				<tbody>					
				<?php //Music Title ?>
					<tr class="base">
						<td class="tle"><label>Music Title:</label></td>
						<td contenteditable='true' id="music_title"><?php if( $GLOBALS['rme_edit'] !=""){ echo $resultsquery[0]->mTitle; } else { if($scraped_musician == ""){ echo "Place something";} else { echo $scraped_musician; } }?></td>
					</tr>
				<?php //Artist ?>
					<tr class="base alt">
						<td class="tle"><label>Artist:</label></td>
						<td contenteditable='true' id="music_artist"><?php if( $GLOBALS['rme_edit'] !=""){ echo $resultsquery[0]->mArtist; } else { if($scraped_song == ""){ echo "Place something";} else { echo $scraped_song; }  }?></td>
					</tr>
				<?php //Album ?>
					<tr class="base">
						<td class="tle"><label>Album:</label></td>
						<td contenteditable='true' id="music_album"><?php if( $GLOBALS['rme_edit'] !=""){ echo $resultsquery[0]->mAlbum; } else { if($scraped_album == ""){ echo "Place something";} else { echo $scraped_album; } } ?></td>
					</tr>				
				<?php //Release Date ?>
					<tr class="base alt">
						<td class="tle"><label>Release Date:</label></td>
						<td contenteditable='true' id="music_rel_date"><?php if( $GLOBALS['rme_edit'] !=""){ echo $resultsquery[0]->mRelease;} else { if($scraped_album == ""){ echo "Place something";} else { echo $scraped_album; }  }?></td>
					</tr>				
				</tbody>
			</table>
		</div>
	</div>
			</form>
</div>

<div class="rmedata">
	<div>
		<form method="post" action="">
			<select name="doaction" value="">
				<option>Delete</option>
				<option>Unmark</option>
			</select>	
			<input type="submit" id="deleteitem" name="doapply" value="Apply"/>
		</form>
	</div>

	<table class="wp-list-table widefat fixed posts"> 
		<thead>
			<tr>
				<th width="50px">&#10004;</th>
					<th><?php _e('Site Name', 'rme-url-shortener'); ?></th>
						<th><?php _e('URL', 'rme-url-shortener'); ?></th>
							<th><?php _e('Title', 'rme-url-shortener'); ?></th>
								<th><?php _e('Image', 'rme-url-shortener'); ?></th>
									<th><?php _e('Description', 'rme-url-shortener'); ?></th>
										<th><?php _e('Shorten Link', 'rme-url-shortener'); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th width="50px"></th>
					<th><?php _e('Site Name', 'rme-url-shortener'); ?></th>
						<th><?php _e('URL', 'rme-url-shortener'); ?></th>
							<th><?php _e('Title', 'rme-url-shortener'); ?></th>
								<th><?php _e('Image', 'rme-url-shortener'); ?></th>
									<th><?php _e('Description', 'rme-url-shortener'); ?></th>
										<th><?php _e('Shorten Link', 'rme-url-shortener'); ?></th>
			</tr>
		</tfoot>	

		<tbody>
			<?php
				foreach($GLOBALS['results'] as $result){

			?>
					<tr>
						<td width="50px">
							<input id="rmemrkitem" type="checkbox" name="rmemrkitem" value="<?php echo $result->urlid; ?>">
						</td>
						<td id="urlsitename" class="rmeitem">
							<?php echo $result->urlsitename; ?>						
							<br/>
							<div class="rmeitem-opt">
								<!--<span class="rme-edit" title="Edit">
								<a href="/wp-admin/admin.php?page=rme_shortener&rme-edit=<?php// echo $result->urlid; ?>">Edit</a> | </span>-->
								<a href="/wp-admin/admin.php?page=rme_shortener&rme_edit=<?=$result->urlid;?>" class="edititemrme">Edit</a> | 
								<a href="/wp-admin/admin.php?page=rme_shortener&urlid=<?=$result->urlid;?>" class="deleteitemrme">Delete</a>
							</div>
						</td>					
						<td id="urlurl">
							<?php echo $result->urlurl;?>
						</td>								 
						<td id="urltitle">
							<?php echo $result->urltitle;?>
						</td>					
						<td id="urlimg">
							<?php echo $result->urlimg;?>
						</td>					
						<td id="urldesc">
							<?php echo $result->urldesc;?>
						</td>
						<td id="urlshorten">
							<?php echo $result->urlshorten;?>
						</td>
					</tr>
			<?php
				}
			?>
		</tbody>
	</table>
				<?php
					if(count($GLOBALS['results']) == $rec_limit){
						if( $page > 0 ){
						   $last = $page - 2;
						   echo "<a href=\"$_PHP_SELF/wp-admin/admin.php?page=rme_shortener&pages=$last\">Previous $rec_limit Records</a> |";
						   echo "<a href=\"$_PHP_SELF/wp-admin/admin.php?page=rme_shortener&pages=$page\">Next $rec_limit Records</a>";
						}
						else if( $page == 0 ){
						   echo "<a href=\"$_PHP_SELF/wp-admin/admin.php?page=rme_shortener&pages=$page\">Next $rec_limit Records</a>";
						}
					}
					else if( count($results) < $rec_limit ){
						$last = $page - 2;
						echo "<a href=\"$_PHP_SELF/wp-admin/admin.php?page=rme_shortener&pages=$last\">Previous $rec_limit Records</a>";				
					}
				?>	
</div>

<div id="upload" style="text-align:center;">
	<h1>Enter URL</h1>
	<input type="text" name="imgurl" id="imgurl" value="" style="width: 95%; padding: 10px; margin-bottom: 20px;"/>
	<input type="button" id="addurl" name="addurl" value="Add Image"/>
</div>