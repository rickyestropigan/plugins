jQuery(document).ready(function() {
	jQuery('#adduser').validator().on('submit', function (e) {
		var pass =  jQuery( "#gtv_password" ).val();
		var conf_pass =  jQuery( "#inputPasswordConfirm" ).val();
		var old_pass =  jQuery( "#gtv_current_password" ).val();
		
		
		 if(old_pass != "" && (pass == "" || conf_pass == "")){
			 jQuery( "#wrap-notify" ).animate({ "height": "44px" }, "slow" );
					    jQuery( "#notify-content" ).text("New Password is required!");
					    setTimeout(function() {jQuery( "#wrap-notify" ).animate({ "height": "0px" }, "slow" );},4000);
						e. preventDefault();
						return;
		 }
		 else  if(pass != conf_pass ){
					  jQuery( "#wrap-notify" ).animate({ "height": "44px" }, "slow" );
					   jQuery( "#notify-content" ).text("Password don't match!");
					   setTimeout(function() {jQuery( "#wrap-notify" ).animate({ "height": "0px" }, "slow" );},4000);
						e. preventDefault();
			}
		 else if(pass != "" || conf_pass != ""){
			  if(old_pass == ""){
				    jQuery( "#wrap-notify" ).animate({ "height": "44px" }, "slow" );
					jQuery( "#notify-content" ).text("Old Password is required!");
					setTimeout(function() {jQuery( "#wrap-notify" ).animate({ "height": "0px" }, "slow" );},4000);
					e. preventDefault();
			 }
			else{
				
			}
		 }
		 else{}
		 /* 
	
		 if(  pass == "" && conf_pass == "" ){
			 
			 jQuery(this).submit();
		 }
		else if(pass == conf_pass ){
			 if(old_pass == ""){
				   jQuery( "#wrap-notify" ).animate({ "height": "44px" }, "slow" );
					   jQuery( "#notify-content" ).text("Old Password is required!");
					   setTimeout(function() {jQuery( "#wrap-notify" ).animate({ "height": "0px" }, "slow" );},4000);
						e. preventDefault();
			 }
			 else if(pass.length >=6 && conf_pass.length >= 6){
				// jQuery(this).submit();
			 }
			 else{
				  jQuery( "#wrap-notify" ).animate({ "height": "44px" }, "slow" );
					   jQuery( "#notify-content" ).text("Minimum of 6 characters.");
					   setTimeout(function() {jQuery( "#wrap-notify" ).animate({ "height": "0px" }, "slow" );},4000);
						e. preventDefault();
			 }
		} 
		else if(pass != conf_pass || (pass != "" && conf_pass != "")){
			 jQuery( "#wrap-notify" ).animate({ "height": "44px" }, "slow" );
		   jQuery( "#notify-content" ).text("Password don't match!");
		   setTimeout(function() {jQuery( "#wrap-notify" ).animate({ "height": "0px" }, "slow" );},4000);
			e. preventDefault();
		}
		else{} */
/*   if (e.isDefaultPrevented()) {
   jQuery( "#wrap-notify" ).animate({ "height": "44px" }, "slow" );
   jQuery( "#notify-content" ).text("sure!");
   setTimeout(function() {jQuery( "#wrap-notify" ).animate({ "height": "0px" }, "slow" );},8000);
  } else {
   
  } */
});
	
	jQuery( ".close" ).click(function() {
		
			jQuery( this).closest('.alert').fadeOut( "slow" );
	});


	
    jQuery('#gravity-table').DataTable(
	 {
    order: [ [
      jQuery('th.defaultSort').index(),
      'disasc'
    ] ],
	  "dom": '<"top"flip>rt<"bottom"ip><"clear">',
	  oLanguage: {
		sSearch: ''
		}
  } 
	);
	 
jQuery('.dataTables_filter input').attr("placeholder", "Search");

jQuery(".tablesaw-bar").appendTo(".top");
	jQuery('.modal-content #details').after("<span id='close_esc'><a class='close_esc' onclick='close_esc()'>Close(Esc)</a></span>");
	 } );
	 

function myFunction(a) {
  	 jQuery('select[name=billing_country_front]').val("Algeria");
	
}
	 
// Get the modal
var modal = document.getElementById('myModal');

// Get the button that opens the modal
var btn = document.getElementById("myBtn");

// Get the <span> element that closes the modal
var span = document.getElementsByClassName("close")[0];
// When the user clicks on the button, open the modal
function redirect(url,get_entry,get_form_id,email)
{
jQuery('.block_content').css("display","block");
jQuery('body').css("overflow-y","hidden");
jQuery.ajax({
		url : payment.ajax_url,
		type : 'post',
		data : {
			action : 'url_payment',
			url :document.URL,
			get_entry :get_entry,
			form_id :get_form_id,
			email: email,
			
		},
		success : function( response ){
			//alert(response);
		//	console.log(response); 
			window.location =response;
		}
});
}
function show_more_detail(title_display,id,form_id,form_title)
{
	
/* 	if(jQuery(window).scrollTop() >= 12 || jQuery(window).scrollTop() == jQuery(document).height()- jQuery(window).height()) {
   // do something
   alert("asdfasdf");
}*/

 /*   var  strx   = title_display.split(',');
    var array  = [];

array = array.concat(strx); */


jQuery( ".modal-content" ).css("opacity","1");
 jQuery( "body" ).css("overflow-y","hidden");
	jQuery('.modal-content #details').html("<div class='loader'></div>");
	jQuery('.modal-content .header-title').html('<h6>'+form_title+'</h6>');
	
	
 //("#details").before().load("http://onlinemarketing.guru/?gf_page=print-entry&fid=1&lid=826 #p1");
	
	
   jQuery.ajax({
		url : postlove.ajax_url,
		type : 'post',
		data : {
			action : 'post_love_add_love',
			post_id : id,
			form_id : form_id
		},
		success : function( response )
		{
			/*  var a =JSON.parse(response);


			Array.prototype.move = function (old_index, new_index) {
				if (new_index >= this.length) {
					var k = new_index - this.length;
					while ((k--) + 1) {
						this.push(undefined);
					}
				}
				this.splice(new_index, 0, this.splice(old_index, 1)[0]);
			  };
			  
			// console.log(a);
		 response.move("Days Completion","form_id"); */
			// console.log(a[0]); 
			
		 var pricecount = 0;
			var table = jQuery('<ul class="more_info"></ul>');
			var repeaterrow_count = 0;
			 jQuery.each(response, function() {
				 jQuery.each(this, function(k, v) 
				 {
				 if(typeof v !='object')
				 {  
						var row = jQuery('<li class="repeater-row"><div class="entry-view-field-name '+k+'">'+k+'</div></li>');
						 if(v != ""  && k  != "id" && k  != "form_id"  && k  != "Days Completion" && k  != "ip" && k != "post_id" && k  != "is_fulfilled" && k  != "created_by" &&  k != "transaction_type"  && k != "user_agent"  && k != "is_user"  && k != "User ID" && k != "status" && k != "currency" && k != "source_url" && k != "is_read" && k.indexOf("(Name)") == -1  && k.indexOf("Approx") == -1  && k.indexOf("Computation") == -1 && k.indexOf("Multiply") == -1 && k.indexOf("Total") == -1  &&  k != "Data"  &&  v != "$ 0.00" && k.indexOf("Conditional") == -1)
					{
						//alert(k);
						
					var id= k;
					var regex = /[^\w\s]/gi;
					var classname = "row";
						if( id.match(/\((.*)\)/) && regex.test(id) == true)
						{
							if(id.indexOf("Price")  != -1)
							{
								 id = id.match(/\((.*)\)/);
								id = id[1].replace(/["'()]/g,"").replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, ' ').replace(/ /g,'').replace("Price","") ;
								classname = "Price";
								id = id  == null ?id : k;
								//id = "Price"+pricecount++;
								
							}
							else
							{
								 id = id.match(/\((.*)\)/);
								id = id[1].replace(/["'()]/g,"").replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, ' ').replace(/ /g,'');
							}
						}
						var get_id = id.replace(/["'()]/g,"").replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, ' ').replace(/ /g,'');
						if("paymentamount" == get_id){
							v = "$ "+v;
							var currency = gf_global['\u0000RGCurrency\u0000currency']['symbol_left'] ;
						
						}
						 var row = jQuery('<li id="'+get_id+'" class="'+classname+'"><div class="entry-view-field-name">'+k.replace(/_/g, ' ')+'</div><div class="entry-view-field-value">'+v+'</div></li>');
						  table.append(row);
					} 
				 }
				});
					//breakercount = 0;
				 jQuery.each(this, function(k, v) {
				
				 if(typeof v =='object')
				 {
					
					 var items = k.split("/~/");
					 var classname = items[1].replace(/["'()]/g,"").replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, ' ').replace(/ /g,'').replace("Price","");
					var row = jQuery('<li  id="repeater-row" class="'+classname+'"><div class="entry-view-field-name field-title'+items[0]+'">'+items[1]+'</div></li>');
					
					jQuery.each(v, function() 
					{
						var breaker ="";
						
						 jQuery.each(this, function(key, value) 
						 {
								 jQuery.each(this, function(key, value) 
								{
									/* if(breaker == key || breaker == "")
									{
										 breaker = key;
										 
										 row.append('<hr>');
										
									} */
										
									if(value != "")
									{
										row.append('<div class="entry-detail-view"><span id="repeater-title" style="font-weight: 600;font-size: 15px;">'+key+' : </span><span>'+value+'</span></div>');
										
									}
								});
								
						});
						
							
							row.append('<hr>');
						// jQuery("li#repeater-row div:last").css( "color", "red!important" );
							
						
							
							
					// breakercount=1;
					}); 
				 }
				 else
				 {
					
					
				 }
				 table.append(row);
				  });
			});
		   
		   var this_url      = window.location.origin;   
		
		   
	  jQuery.ajax({
          url:this_url,
       type:"GET",
        dataType: "html",
        contentType: "html",
		data : {
			gf_page : "print-entry",
			fid : form_id,
			lid : id,
		},
        success: function (response) {
			jQuery('.modal-content #details').append("<div id='order-details'></div>");
			 var html_order ;
			//var success =  jQuery(jQuery.parseHTML(response)).filter(".lastrow .entry-products"); 
		var response = jQuery('<html />').html(response)
		  var  html_order = response.find('.lastrow');
			jQuery.each(html_order, function (index, item) {
         html_order = item.innerHTML;
			jQuery(".more_info").before(jQuery("#order-details"));
  });
  //console.log(html_order);
		  jQuery('.loader').remove();
		  jQuery('#order-details').html(html_order);
		  jQuery('.modal-content #details').append(table);
		  	jQuery("li.BasicDistributionto50Sitesprojectdetails").before(jQuery("li#Distributionto50Sites.row"));
			jQuery("li.PremiumDistributionto500Sitesprojectdetails").before(jQuery("li#Distributionto500Sites.row"));
			
			jQuery("li.DA10Websitesprojectdetails").before(jQuery("li#DA10Websites.row"));
			jQuery("li.DA20Websitesprojectdetails").before(jQuery("li#DA20Websites.row"));
			jQuery("li.DA30Websitesprojectdetails").before(jQuery("li#DA30Websites.row"));
			jQuery("li.DA10WebsitesPaydayLoansGamblingprojectdetails").before(jQuery("li#PaydayLoansGamblingPharma.row"));
			
			jQuery("li.25Citationsprojectdetails").after(jQuery("li.50Citationsprojectdetails"));
			
			jQuery("li.25Citationsprojectdetails").before(jQuery("li#25Citations.row"));
			jQuery("li.50Citationsprojectdetails").before(jQuery("li#50Citations.row"));
			jQuery("li.100Citationsprojectdetails").before(jQuery("li#100Citations.row"));
			
	 		jQuery("li#100Citations.row").before(jQuery("li#100CitationsPricePrice.Price"));
			jQuery("li#50Citations.row").before(jQuery("li#50CitationsPricePrice.Price"));
			jQuery("li#25Citations.row").before(jQuery("li#25CitationsPricePrice.Price")); 
			
			
			jQuery("li.50Citationsprojectdetails").before(jQuery("li#50CitationsPricePrice.Price")); 
		
			jQuery("li#Distributionto50Sites.row").before(jQuery("li#BasicDistributionto50SitesPrice.Price"));
			jQuery("li#Distributionto500Sites.row").before(jQuery("li#PremiumDistributionto500SitesPrice.Price"));
			
			jQuery("li#DA10Websites.row").before(jQuery("li#DA10WebsitesPrice.Price"));
			jQuery("li#DA20Websites.row").before(jQuery("li#DA20WebsitesPrice.Price"));
			jQuery("li#DA30Websites.row").before(jQuery("li#DA30WebsitesPrice.Price"));
			jQuery("li#PaydayLoansGamblingPharma.row").before(jQuery("li#DA10WebsitesPaydayLoansGamblingPharmaPrice.Price"));
			
			jQuery(".more_info li:last").after(jQuery("li#FirstName.row"));
			jQuery(".more_info li:last").after(jQuery("li#LastName.row"));
			jQuery(".more_info li:last").after(jQuery("li#Firstname.row"));
			jQuery(".more_info li:last").after(jQuery("li#Lastname.row"));
			jQuery(".more_info li:last").after(jQuery("li#First.row"));
			jQuery(".more_info li:last").after(jQuery("li#Last.row"));
			jQuery(".more_info li:last").after(jQuery("li#Email.row"));
			jQuery(".more_info li:last").after(jQuery("li#EnterEmail.row"));
			jQuery(".more_info li:last").after(jQuery("li#BillingCountry.row"));
			jQuery(".more_info li:last").after(jQuery("li#Phone.row"));
			jQuery(".more_info li:last").after(jQuery("li#Website.row"));
        },
        error: function (response) {
        }
    });
		
			
			
	/* 	jQuery( ".more_info" ).load(function() {
		  // Handler for .ready() called.
		  
		}); */
			
	     
		}
	});
	
	  modal.style.display = "block";
	
}
// When the user clicks on <span> (x), close the modal
if( span != null || close_esc != null )
	{
span.onclick = function() {
	if( modal != null )
	{
		modal.style.display = "none";
	 jQuery( "body" ).css("overflow-y","auto");
	}
}
function close_esc() {
	if( modal != null )
	{
		modal.style.display = "none";
	 jQuery( "body" ).css("overflow-y","auto");
	}
    
}
}
document.onkeydown = function(evt) {
    evt = evt || window.event;
    if (evt.keyCode == 27) {
       if( modal != null )
		{
			modal.style.display = "none";
		 jQuery( "body" ).css("overflow-y","auto");
		}
    }
};
// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
		 jQuery( "body" ).css("overflow-y","auto");
	
    }
}
// Function to create a table as a child of el.
// data must be an array of arrays (outer array is rows).
function arrayToTable(tableData) {
    
    return table;
}
function isEmpty(value) {
  return typeof value == 'string' && !value.trim() || typeof value == 'undefined' || value === null;
}



