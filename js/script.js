/**
 *
 * Crop Image While Uploading With jQuery
 * 
 * Copyright 2013, Resalat Haque
 * http://www.w3bees.com/
 *
 */

// set info for cropping image using hidden fields
function setInfo(i, e) {
	$('#x').val(e.x1);
	$('#y').val(e.y1);
	$('#w').val(e.width);
	$('#h').val(e.height);
}

$(document).ready(function() {
	$('#is_enddate').click(function(){		
		if($(this).is(':checked')==false){
			var today = new Date();
			var dd = today.getDate();
			var mm = today.getMonth()+1; //January is 0!
			var yyyy = today.getFullYear();
			
			if(dd<10) {
			    dd='0'+dd
			} 
			
			if(mm<10) {
			    mm='0'+mm
			} 
			
			today = mm+'-'+dd+'-'+yyyy			
			
			var item_id = $('#tempid').val();
			$.ajax({  
              type: "POST", url: 'removeenddate.php', data: "id="+item_id, async: false,  
              complete: function(data){
			$('#enddate_new').val(today);
		
		   
              }  
          });
			NewCal('enddate_new','mmddyyyy','overlay')
		}
	});
	if(document.getElementById('cal1Container')!=null){
		NewCal('enddate_new','mmddyyyy','overlay')
	}
	var p = $("#uploadimage");

	// prepare instant preview
	$("#uploadfile").change(function(){
		
		// fadeOut or hide preview
		p.fadeOut();

		// prepare HTML5 FileReader
		var oFReader = new FileReader();
		//alert(document.getElementById("uploadfile").files[0]);
		//console.log(document.getElementById("uploadfile").files[0]);
		
		if(document.getElementById("uploadfile").files[0]['type']=='image/png' || document.getElementById("uploadfile").files[0]['type']=='image/gif' || document.getElementById("uploadfile").files[0]['type']=='image/jpg' || document.getElementById("uploadfile").files[0]['type']=='image/jpeg'){
			
		$("#cropbutton").show();
		oFReader.readAsDataURL(document.getElementById("uploadfile").files[0]);
		
		oFReader.onload = function (oFREvent) {
		//alert(oFREvent.target.result);
		p.attr('value', oFREvent.target.result).fadeIn();
		}
			
		}else{
			$("#cropbutton").hide();
		};
	});

	// implement imgAreaSelect plug in (http://odyniec.net/projects/imgareaselect/)
	/*$('img#uploadPreview').imgAreaSelect({
		 //set crop ratio (optional)
		aspectRatio: '1:1',
		onSelectEnd: setInfo
	});*/
	
	
	
	
});

function update_temp_carrausel(id, date_val){
	var cid = id.split("_");
	var item_id=cid[1];
	window.opener.document.getElementById(id).value=date_val
	window.opener.document.getElementById('date_'+item_id).value=date_val
	 $.ajax({  
              type: "POST", url: 'addenddate.php', data: "id="+item_id+"&enddate="+date_val, async: false,  
              complete: function(data){
		var color_scheme = data.responseText.trim();		
		setTimeout(function(){
			$(window.opener.document.getElementById('img_'+item_id)).attr('src','./img/'+color_scheme+'/calendar.png');
			 window.close();	   
			}, 500);
		   
              }  
          });
}
function closeMe(id){
	var cid = id.split("_");
	var item_id=cid[1];
	var color_scheme = $(window.opener.document.getElementById('color_scheme_'+item_id)).val();
	
	
	 $.ajax({  
              type: "POST", url: 'removeenddate.php', data: "id="+item_id, async: false,  
              complete: function(data){
			
		setTimeout(function(){
			$(window.opener.document.getElementById(id)).val('');
	$(window.opener.document.getElementById('img_'+item_id)).attr('src','./img/'+color_scheme+'/calendar_inactive.png');
			 window.close();	   
			}, 500);
		   
              }  
          });
	
	
}

function changeformat(date_val){
	
	var date= date_val.split("-");
	date=date['1']+"-"+date['0']+"-"+date['2'];
	$('#enddate').val(date);
	
}


function get_data_from_csv(id){
	
 var filetype=$('#filetype').val();
 var obj;
     $.ajax({  
              type: "POST", url: 'givecsvdata.php', data: {id : id, filetype: filetype},
              complete: function(data){
		  obj = $.parseJSON(data.responseText);
		  //alert(data.responseText);
		 //alert(obj.enddate);
		 $('#enddate').val(obj.enddate);
		 $('#enddate_new').val(obj.selected_date);
		 NewCal('enddate_new','mmddyyyy','overlay')
		  var maxlength1 = $('#line1').attr('maxlength')
		   var valuelength1= obj.line1.length;
		   $('#line1').val(obj.line1);
		 
		    if(valuelength1>maxlength1){
	   $('#line1').addClass('timeline_length_error ');
	 }else{
	  $('#line1').removeClass('timeline_length_error');
	  $('#line1').parent('div').find('div.timeline_inputsize_error_msg').hide('slow', function(){ $('#line1').parent('div').find('div.timeline_inputsize_error_msg').remove(); });
	 }
		  
		  
		  var maxlength2 = $('#line2').attr('maxlength')
		   var valuelength2= obj.line2.length;
		   $('#line2').val(obj.line2);
		 
		    if(valuelength2>maxlength2){
	   $('#line2').addClass('timeline_length_error ');
	 }else{
	  $('#line2').removeClass('timeline_length_error');
	  $('#line2').parent('div').find('div.timeline_inputsize_error_msg').hide('slow', function(){ $('#line2').parent('div').find('div.timeline_inputsize_error_msg').remove(); });
	 }
		  
		    var maxlength3 = $('#line3').attr('maxlength')
		   var valuelength3= obj.line3.length;
		   $('#line3').val(obj.line3);
		 
		    if(valuelength3>maxlength3){
	   $('#line3').addClass('timeline_length_error ');
	 }else{
	  $('#line3').removeClass('timeline_length_error');
	  $('#line3').parent('div').find('div.timeline_inputsize_error_msg').hide('slow', function(){ $('#line3').parent('div').find('div.timeline_inputsize_error_msg').remove(); });
	 }
		  
		  
		     var maxlength4 = $('#line4').attr('maxlength')
		   var valuelength4= obj.line4.length;
		   $('#line4').val(obj.line4);
		 
		    if(valuelength4>maxlength4){
	   $('#line4').addClass('timeline_length_error ');
	 }else{
	  $('#line4').removeClass('timeline_length_error');
	  $('#line4').parent('div').find('div.timeline_inputsize_error_msg').hide('slow', function(){ $('#line4').parent('div').find('div.timeline_inputsize_error_msg').remove(); });
	 }
	 
	   var maxlength5 = $('#line5').attr('maxlength')
		   var valuelength5= obj.line5.length;
		   $('#line5').val(obj.line5);
		 
		    if(valuelength5>maxlength5){
	   $('#line5').addClass('timeline_length_error ');
	 }else{
	  $('#line5').removeClass('timeline_length_error');
	  $('#line5').parent('div').find('div.timeline_inputsize_error_msg').hide('slow', function(){ $('#line5').parent('div').find('div.timeline_inputsize_error_msg').remove(); });
	 }
	 
	   var maxlength6 = $('#line6').attr('maxlength')
		   var valuelength6= obj.line6.length;
		   $('#line6').val(obj.line6);
		 
		    if(valuelength6>maxlength6){
	   $('#line6').addClass('timeline_length_error ');
	 }else{
	  $('#line6').removeClass('timeline_length_error');
	  $('#line6').parent('div').find('div.timeline_inputsize_error_msg').hide('slow', function(){ $('#line6').parent('div').find('div.timeline_inputsize_error_msg').remove(); });
	 }
	 
	 
	   var maxlength7 = $('#line7').attr('maxlength')
		   var valuelength7= obj.line7.length;
		   $('#line7').val(obj.line7);
		 
		    if(valuelength7>maxlength7){
	   $('#line7').addClass('timeline_length_error ');
	 }else{
	  $('#line7').removeClass('timeline_length_error');
	  $('#line7').parent('div').find('div.timeline_inputsize_error_msg').hide('slow', function(){ $('#line7').parent('div').find('div.timeline_inputsize_error_msg').remove(); });
	 }
		
	 var maxlength8 = $('#line8').attr('maxlength')
		   var valuelength8= obj.line8.length;
		   $('#line8').val(obj.line8);
		 
		    if(valuelength8>maxlength8){
	   $('#line8').addClass('timeline_length_error ');
	 }else{
	  $('#line8').removeClass('timeline_length_error');
	  $('#line8').parent('div').find('div.timeline_inputsize_error_msg').hide('slow', function(){ $('#line8').parent('div').find('div.timeline_inputsize_error_msg').remove(); });
	 }
              }  
          });
    
}




(function( $ ) {
		$.widget( "ui.combobox", {
			_create: function() {
				var self = this,
					select = this.element.hide(),
					selected = select.children( ":selected" ),
					value = selected.val() ? selected.text() : "";
				var input = this.input = $( "<input>" )
					.insertAfter( select )
					.val( value )
					.autocomplete({
						delay: 0,
						minLength: 0,
						source: function( request, response ) {
							var matcher = new RegExp( $.ui.autocomplete.escapeRegex(request.term), "i" );
							response( select.children( "option" ).map(function() {
								var text = $( this ).text();
								if ( this.value && ( !request.term || matcher.test(text) ) )
									return {
										label: text.replace(
											new RegExp(
												"(?![^&;]+;)(?!<[^<>]*)(" +
												$.ui.autocomplete.escapeRegex(request.term) +
												")(?![^<>]*>)(?![^&;]+;)", "gi"
											), "<strong>$1</strong>" ),
										value: text,
										option: this
									};
							}) );
						},
						select: function( event, ui ) {
							
							get_data_from_csv(ui.item.option.value);
							ui.item.option.selected = true;
							
							
							console.log(ui.item.option.value);
							self._trigger( "selected", event, {
								item: ui.item.option
							});
							
						},
						change: function( event, ui ) {
							
							if ( !ui.item ) {
								var matcher = new RegExp( "^" + $.ui.autocomplete.escapeRegex( $(this).val() ) + "$", "i" ),
									valid = false;
								select.children( "option" ).each(function() {
									if ( $( this ).text().match( matcher ) ) {
										this.selected = valid = true;
										
										
										return false;
									}
								});
								if ( !valid ) {
									// remove invalid value, as it didn't match anything
									$( this ).val( "" );
									select.val( "" );
									input.data( "autocomplete" ).term = "";
									return false;
								}
							}
							
							
						}
					})
					.addClass( "ui-widget ui-widget-content ui-corner-left" );

				input.data( "autocomplete" )._renderItem = function( ul, item ) {
					return $( "<li></li>" )
						.data( "item.autocomplete", item )
						.append( "<a>" + item.label + "</a>" )
						.appendTo( ul );
				};

				this.button = $( "<button>&nbsp;</button>" )
					.attr( "tabIndex", -1 )
					.attr( "title", "Show All Items" )
					.insertAfter( input )
					.button({
						icons: {
							primary: "ui-icon-triangle-1-s"
						},
						text: false
					})
					.removeClass( "ui-corner-all" )
					.addClass( "ui-corner-right ui-button-icon" )
					.click(function() {
						// close if already visible
						if ( input.autocomplete( "widget" ).is( ":visible" ) ) {
							input.autocomplete( "close" );
							return;
						}

						// pass empty string as value to search for, displaying all results
						input.autocomplete( "search", "" );
						input.focus();
					});
			},

			destroy: function() {
				this.input.remove();
				this.button.remove();
				this.element.show();
				$.Widget.prototype.destroy.call( this );
			}
		});
	})( jQuery );
