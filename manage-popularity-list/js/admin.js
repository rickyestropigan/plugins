jQuery(function($){
	//alert("clicked");
	$(".radio-filter").click(function(){
		$(".hidden-widget-element").hide();
		console.log("clicked");
		if($(this).hasClass("pop-tagged-post") || $(this).hasClass("recent-tagged-post")){
			$(this).parent().parent().find('.tagged-post').show();
		}
		if($(this).hasClass("pop-cat-post") || $(this).hasClass("recent-cat-post")){
			$(this).parent().parent().find('.categories-post').show();
		}
	})
})