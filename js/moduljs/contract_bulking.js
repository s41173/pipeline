$(document).ready(function (e) {
	
    // function general
	
	$('#datatable-buttons').dataTable({
	 dom: 'T<"clear">lfrtip',
		tableTools: {"sSwfPath": site}
	 });
	 
	// date time picker
	  $('#d1,#d2,#d3,#d4,#d5,#d6').daterangepicker({
		 locale: {format: 'YYYY/MM/DD'}
		}); 
		
	$('#ds1,#ds2,#ds3').daterangepicker({
			locale: {format: 'YYYY-MM-DD'},
	    singleDatePicker: true,
			showDropdowns: true
	 });

	 $('#dtime1,#dtime2,#dtime3,#dtime4,#dtime5,#dtime6').daterangepicker({
		timePicker: true,
		singleDatePicker: true,
		showDropdowns: true,
		timePicker24Hour: true,
		locale: { format: 'YYYY/MM/DD H:mm'}
	 });
	
	load_data();  


	// checkbox set empty 

	// form submit update
		// ajax form non upload data
	$("#upload_form_update,#upload_form_update2").on('submit',(function(e) {
		
			var elem = $(this);
			e.preventDefault();
			$.ajax({
				url: $(this).attr('action'),
				type: "POST",
				data:  new FormData(this),
				contentType: false,
				cache: false,
				processData:false,
				beforeSend : function()
				{
					//$("#preview").fadeOut();
				},
				success: function(data)
				{
					// console.log(data);
					res = data.split("|");
					
					if(res[0]=='true')
					{
						// invalid file format.
						error_mess(1,res[1]);
						// if (elem.attr('id') == "upload_form_non"){ resets(); }
					}
					else if(res[0] == 'warning'){ error_mess(2,res[1]); }
					else if(res[0] == 'error'){ error_mess(3,res[1]); }
				},
				  error: function(e) 
				{
					//$("#error").html(e).fadeIn();
					error_mess(3,e);
					console.log(e.responseText);	
				} 	        
		   });
			 
		}));
	
	// batas dtatable
	
	// fungsi jquery update
	$(document).on('click','.text-primary',function(e)
	{	
		e.preventDefault();
		var element = $(this);
		var del_id = element.attr("id");
		var url = sites_get +"/"+ del_id;
		
		window.location.href = url;
		
	});

	// density form updadte
	$('#densityform').submit(function() {
		$.ajax({
			type: 'POST',
			url: $(this).attr('action'),
			data:  new FormData(this),
			contentType: false,
    	cache: false,
			processData:false,
			success: function(data) {
				
				res = data.split("|");
				if (res[0] == "true")
				{   
			      error_mess(1,res[1],0);
					  location.reload(true);
				}
				else if (res[0] == 'warning'){ error_mess(2,res[1],0); }
				else{ error_mess(3,res[1],0); }
			},
			error: function(e) 
	    	{
				$("#error").html(e).fadeIn();
				console.log(e.responseText);	
	    	} 
		})
		return false;
	});
	
		// fungsi jquery update
	$(document).on('click','.text-ledger',function(e)
	{	
		e.preventDefault();
		var element = $(this);
		var del_id = element.attr("id");
		var url = sites_ledger +"/"+ del_id;
		
		window.open(url, "_blank", "scrollbars=1,resizable=0,height=600,width=800");
	});
	
	// fungsi attribute status
	$(document).on('click','.text-attribute',function(e)
	{	
		e.preventDefault();
		var element = $(this);
		var del_id = element.attr("id");
		var url = sites_attribute +"/"+ del_id;
		$(".error").fadeOut();
		
		console.log(url);
		
		$("#myModal2").modal('show');
		$('#frame').attr('src',url);
		$('#frame_title').html('Product Attribute');	
	});

		// fungsi detail status
	$(document).on('click','.text-details',function(e)
	{	
		e.preventDefault();
		var element = $(this);
		var del_id = element.attr("id");
		var url = sites_details +"/"+ del_id;
		$(".error").fadeOut();
		
		$("#myModal9").modal('show');
		$.post(url,
			{id:$(this).attr('data-id')},
			function(result)
			{
				 res = result.split("|");
				
			   $("#sku").html(res[0]);
			   $("#name").html(res[1]);
			   $("#model").html(res[2]);
			   $("#description").html(res[3]);
				 $("#status").html(res[4]);
				 $("#dimension").html(res[5]);
				 $("#weight").html(res[6]);
				 $("#qty").html(res[7]);
			   $("#minorder").html(res[8]);
				 $("#content").html(res[9]);
				 $("#height").html(res[10]);
				 $("#measuring").html(res[11]);
				 $("#temperature").html(res[12]);
				 $("#extrakg").html(res[13]);
				 $("#extrapercentage").html(res[14]);
				 $("#apiid").html(res[15]);
			   
			}   
		);
		// ajax end
	});
	
	$(document).on('click','.text-img',function(e)
	{	
		e.preventDefault();
		var element = $(this);
		var del_id = element.attr("id");
		var url = sites_image +"/"+ del_id;
		$(".error").fadeOut();
		
		console.log(url);
		
		$("#myModal2").modal('show');
		$('#frame').attr('src',url);
		$('#frame_title').html('Product Image');	
	});
	
	// publish status
	$(document).on('click','.primary_status',function(e)
	{	
		e.preventDefault();
		var element = $(this);
		var del_id = element.attr("id");
		var url = sites_primary +"/"+ del_id;
		$(".error").fadeOut();
		
		// $("#myModal2").modal('show');
		// batas
		$.ajax({
			type: 'POST',
			url: url,
      cache: false,
			headers: { "cache-control": "no-cache" },
			success: function(result) {
				
				res = result.split("|");
				if (res[0] == "true")
				{   
			    error_mess(1,res[1],0);
					load_data();
				}
				else if (res[0] == 'warning'){ error_mess(2,res[1],0); }
				else{ error_mess(3,res[1],0); }
			}
		})
		return false;	
	});

	$('#bset').click(function() {
		
		var urx = sites+"/set_param";

		var cat = $("#ccategory").val();
		var size = $("#csize").val();
		var color = $("#ccolor").val();
		var publish = $("#cpublish").val();

		$.ajax({
			type: 'POST',
			url: urx,
		    data: "category=" + cat + "&size=" + size + "&color=" + color + "&publish=" + publish,
			success: function(data) {

				res = data.split("|");
				if (res[0] == "true")
				{
					error_mess(1,res[1],0);
				}
				else if(res[0] == 'error') { error_mess(3,res[1],0); }
				else{ 
				  error_mess(2,res[1],0);
			    }

			},
			error: function(e) 
			{
				$("#error").html(e).fadeIn();
				console.log(e.responseText);	
			} 
		})
		return false;
		
	});

	$('#cekallupdate').click(function() {
		
		var value = $('input[name="cek[]"]:checked');

		$.ajax({
			type: 'POST',
			url: sites_update,
			data: value,
			success: function(data) {

				res = data.split("|");
				if (res[0] == "true")
				{
					load_data();
					error_mess(1,res[1],0);
				}
				else if(res[0] == 'error') { error_mess(3,res[1],0); }
				else{ 
				  load_data();
				  error_mess(2,res[1],0);
			    }
			},
			error: function(e) 
			{
				$("#error").html(e).fadeIn();
				console.log(e.responseText);	
			} 
		})
		return false;
	});

	
	$('#searchform').submit(function() {
		var date = $("#ds1").val();
		var cust = $("#ccust_search").val();
		var type = $("#ctype_search").val();
		var param = ['searching',date,cust,type];
		
		$.ajax({
			type: 'POST',
			url: $(this).attr('action'),
			data:  new FormData(this),
			contentType: false,
      cache: false,
			processData:false,
			success: function(data) {
				
				if (!param[1]){ param[1] = 'null'; }
				if (!param[2]){ param[2] = 'null'; }
				if (!param[3]){ param[3] = 'null'; }
				load_data_search(param);
			}
		})
		return false;
		swal('Error Load Data...!', "", "error");
		
	});
	
		
// document ready end	
});

	function load_data_search(search=null)
	{
		$(document).ready(function (e) {
			
			var oTable = $('#datatable-buttons').dataTable();
			var stts = 'btn btn-danger';
			
        // console.log(source+"/"+search[0]+"/"+search[1]+"/"+search[2]+"/"+search[3]);

		    $.ajax({
				type : 'GET',
				url: source+"/"+search[0]+"/"+search[1]+"/"+search[2]+"/"+search[3],
				//force to handle it as text
				contentType: "application/json",
				dataType: "json",
				success: function(s) 
				{   
				       console.log(s);
					  
						oTable.fnClearTable();
						$(".chkselect").remove()
	
		$("#chkbox").append('<input type="checkbox" name="newsletter" value="accept1" onclick="cekall('+s.length+')" id="chkselect" class="chkselect">');
							
		for(var i = 0; i < s.length; i++) {
			if (s[i][7] == 1){ stts = 'btn btn-success'; }else { stts = 'btn btn-danger'; }
			oTable.fnAddData([
'<input type="checkbox" class="cek" name="cek[]" value="'+s[i][0]+'" id="cek'+i+'" style="margin:0px"  />',
						i+1,
						s[i][1],
						s[i][2],
						s[i][4],
						s[i][3],
						s[i][6],
						
'<div class="btn-group" role"group">'+
'<a href="" class="'+stts+' btn-xs primary_status" id="' +s[i][0]+ '" title="Primary Status"> <i class="fa fa-power-off"> </i> </a> '+
'<a href="" class="btn btn-primary btn-xs text-primary" id="' +s[i][0]+ '" title=""> <i class="fa fas-2x fa-edit"> </i> </a> '+
'<a href="" class="btn btn-warning btn-xs text-ledger" id="' +s[i][0]+ '" title=""> <i class="fa fas-2x fa-book"> </i> </a> '+
'<a href="#" class="btn btn-danger btn-xs text-danger" id="'+s[i][0]+'" title="delete"> <i class="fa fas-2x fa-trash"> </i> </a>'+
'</div>'
								]);										
							} // End For 
											
				},
				error: function(e){
				   oTable.fnClearTable();  
				   //console.log(e.responseText);	
				}
				
			});  // end document ready	
			
        });
	}

	// load deleted
			function load_deleted()
			{
				$(document).ready(function (e) {
					
					var oTable = $('#datatable-buttons').dataTable();
					var stts = 'btn btn-danger';
					
						$.ajax({
						type : 'GET',
						url: source+"/deleted",
						//force to handle it as text
						contentType: "application/json",
						dataType: "json",
						success: function(s) 
						{   
									 console.log(s);
								
								oTable.fnClearTable();
								$(".chkselect").remove()
			
				$("#chkbox").append('<input type="checkbox" name="newsletter" value="accept1" onclick="cekall('+s.length+')" id="chkselect" class="chkselect">');
									
									for(var i = 0; i < s.length; i++) {
									if (s[i][7] == 1){ stts = 'btn btn-success'; }else { stts = 'btn btn-danger'; }
									oTable.fnAddData([
						'<input type="checkbox" class="cek" name="cek[]" value="'+s[i][0]+'" id="cek'+i+'" style="margin:0px"  />',
												i+1,
												s[i][1],
												s[i][2],
												s[i][4],
												s[i][3],
												s[i][6],
		'<div class="btn-group" role"group">'+
		'<a href="" class="btn btn-success btn-xs text-details" id="' +s[i][0]+ '" title=""> <i class="fa fa-desktop"> </i> </a>'+
		'<a href="#" class="btn btn-danger btn-xs text-danger" id="'+s[i][0]+'" title="delete"> <i class="fa fas-2x fa-trash"> </i> </a>'+
		'</div>'
														]);										
													} // End For 
													
						},
						error: function(e){
							 oTable.fnClearTable();  
							 console.log(e.responseText);	
						}
						
					});  // end document ready	
					
						});
			}

    // fungsi load data
	function load_data()
	{
		$(document).ready(function (e) {
			
			var oTable = $('#datatable-buttons').dataTable();
			var stts = 'btn btn-danger';
			
		    $.ajax({
				type : 'GET',
				url: source,
				//force to handle it as text
				contentType: "application/json",
				dataType: "json",
				success: function(s) 
				{   
				       console.log(s);
					  
						oTable.fnClearTable();
						$(".chkselect").remove()
	
		$("#chkbox").append('<input type="checkbox" name="newsletter" value="accept1" onclick="cekall('+s.length+')" id="chkselect" class="chkselect">');
							
							for(var i = 0; i < s.length; i++) {
						  if (s[i][7] == 1){ stts = 'btn btn-success'; }else { stts = 'btn btn-danger'; }
						  oTable.fnAddData([
        '<input type="checkbox" class="cek" name="cek[]" value="'+s[i][0]+'" id="cek'+i+'" style="margin:0px"  />',
										i+1,
										s[i][1],
										s[i][2],
										s[i][3],
										s[i][4],
										s[i][6],
										
'<div class="btn-group" role"group">'+
'<a href="" class="'+stts+' btn-xs primary_status" id="' +s[i][0]+ '" title="Primary Status"> <i class="fa fa-power-off"> </i> </a> '+
'<a href="" class="btn btn-primary btn-xs text-primary" id="' +s[i][0]+ '" title=""> <i class="fa fas-2x fa-edit"> </i> </a> '+
'<a href="" class="btn btn-warning btn-xs text-ledger" id="' +s[i][0]+ '" title=""> <i class="fa fas-2x fa-book"> </i> </a> '+
'<a href="#" class="btn btn-danger btn-xs text-danger" id="'+s[i][0]+'" title="delete"> <i class="fa fas-2x fa-trash"> </i> </a>'+
'</div>'
										    ]);										
											} // End For 
											
				},
				error: function(e){
				   oTable.fnClearTable();  
				   console.log(e.responseText);	
				}
				
			});  // end document ready	
			
        });
	}
	
	// batas fungsi load data
	function resets()
	{  
	   $(document).ready(function (e) {
		  // reset form
		  // $("#tno, #tmodel, #tsku").val("");
			// $("#catimg").attr("src","");
			$("#breset").click();
	  });
	}
	
	function set_empty(ck,target){
		$(document).ready(function (e) {

			console.log(ck);

		})
	}
	