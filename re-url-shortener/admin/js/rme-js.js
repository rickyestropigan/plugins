jQuery(window).load(function(){
	rmechange();
});
var formfield;
jQuery(document).ready(function(){
/* -------->START<--------- */
 	jQuery('#upload_image_button').click(function() {
		formfield = jQuery('#upload_image').attr('name');
		tb_show('', 'media-upload.php?type=image&TB_iframe=true');
		return false;
	});

	window.send_to_editor = function(html) {
		imgurl = jQuery('img',html).attr('src');
		jQuery('#upload_image').val(imgurl);
		tb_remove();
	}
/*  -------->END<--------- */

	if(jQuery('td').children('.article')){
		jQuery("td .article").closest("tr").hide();
	}
	if(jQuery('td').children('.book')){
		jQuery("td .book").closest("tr").hide();
	}
	if(jQuery('td').children('.profile')){
		jQuery("td .profile").closest("tr").hide();
	}
	if(jQuery('td').children('.video')){
		jQuery("td .video").closest("tr").hide();
	}
	if(jQuery('td').children('.music')){
		jQuery("td .music").closest("tr").hide();
	}
	/*  */
	
	jQuery("#rme_links_custom_type").change(rmechange);
	
	jQuery("#rme_links_custom_campaign_url").on("change", function(){
		jQuery("#loader").show();
		jQuery.post(ajaxurl, { action:'do_scraper', url:jQuery('#rme_links_custom_campaign_url').val() }, function(response) {
			if(response == 'invalid'){
				alert("Please Enter a valid URL");
				jQuery('#rme_links_custom_campaign_url').addClass('error');
				jQuery("#loader").hide();
				return false;
			}else{jQuery('#rme_links_custom_campaign_url').removeClass('error');}
			jQuery("#rme_links_custom_campaign_description").val(jQuery.parseJSON(response.slice(0, -1)).description);
			jQuery.parseJSON(response.slice(0, -1)).url;
			jQuery('#upload_image').val(jQuery.parseJSON(response.slice(0, -1)).image);
			jQuery('#rme_links_custom_type').val(jQuery.parseJSON(response.slice(0, -1)).type);
			jQuery('#rme_links_custom_campaign_vu').val(jQuery.parseJSON(response.slice(0, -1)).video_url);
			jQuery('#rme_links_custom_campaign_vsu').val(jQuery.parseJSON(response.slice(0, -1)).video_secure_url);
			jQuery('#rme_links_custom_campaign_vt').val(jQuery.parseJSON(response.slice(0, -1)).video_type);
			jQuery('#rme_links_custom_campaign_vw').val(jQuery.parseJSON(response.slice(0, -1)).video_width);
			jQuery('#rme_links_custom_campaign_vh').val(jQuery.parseJSON(response.slice(0, -1)).video_height);
			jQuery('#rme_links_custom_campaign_title').val(jQuery.parseJSON(response.slice(0, -1)).title);						jQuery('#title-prompt-text').addClass('screen-reader-text');						jQuery('#rme_links_custom_campaign_title').val(jQuery.parseJSON(response.slice(0, -1)).title);						jQuery('#title').val(jQuery.parseJSON(response.slice(0, -1)).title);			
			rmechange();
			jQuery("#loader").hide();
		});
	});
});
function rmechange(){
		if(jQuery( "#rme_links_custom_type option:selected" ).val() == "music"){
			if(jQuery('td').children('.music')){
				jQuery("td .music,td .video,td .article,td .book,td .profile").closest("tr").hide();
				jQuery("td .music").closest("tr").show();
			}		
		}
		else if(jQuery( "#rme_links_custom_type option:selected" ).val() == "video"){
			if(jQuery('td').children('.video')){
				jQuery("td .music,td .video,td .article,td .book,td .profile").closest("tr").hide();
				jQuery("td .video").closest("tr").show();
			}		
		}
		else if(jQuery( "#rme_links_custom_type option:selected" ).val() == "article"){
			if(jQuery('td').children('.article')){
				jQuery("td .music,td .video,td .article,td .book,td .profile").closest("tr").hide();
				jQuery("td .article").closest("tr").show();
			}		
		}
		else if(jQuery( "#rme_links_custom_type option:selected" ).val() == "book"){
			if(jQuery('td').children('.book')){
				jQuery("td .music,td .video,td .article,td .book,td .profile").closest("tr").hide();
				jQuery("td .book").closest("tr").show();
			}		
		}
		else if(jQuery( "#rme_links_custom_type option:selected" ).val() == "profile"){
			if(jQuery('td').children('.profile')){
				jQuery("td .music,td .video,td .article,td .book,td .profile").closest("tr").hide();
				jQuery("td .profile").closest("tr").show();
			}		
		}
		else{
			jQuery("td .music,td .video,td .article,td .book,td .profile").closest("tr").hide();
		}
		
	}