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

	 $('#ds4,#ds5,#ds6,#ds7,#ds8').daterangepicker({
		timePicker: true,
		singleDatePicker: true,
		showDropdowns: true,
		timePicker24Hour: true,
		locale: { format: 'YYYY-MM-DD H:mm'}
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

	
		// fungsi jquery update
	$(document).on('click','.text-ledger',function(e)
	{	
		e.preventDefault();
		var element = $(this);
		var del_id = element.attr("id");
		var url = sites_details +"/"+ del_id;
		// console.log(url);
		window.location.href = url;
	});

	// validate status
	$(document).on('click','#validatebtn',function(e)
	{	
		e.preventDefault();
		var element = $(this);
		var value = $(this).val();
		var url = sites +"/validation/"+ value;
		$(".error").fadeOut();
		
		// batas
		 $.ajax({
			type: 'POST',
			url: url,
    	    cache: false,
			headers: { "cache-control": "no-cache" },
			success: function(result) {
				
				res = result.split("|");
				if (res[0] == "true")
				{   error_mess(1,res[1],0);
					location.reload();
				}
				else if (res[0] == 'warning'){ error_mess(2,res[1],0); }
				else{ error_mess(3,res[1],0); }
			}
		})
		return false;	
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

	$(document).on('click','.text-remove',function(e)
	{	
		e.preventDefault();
		var element = $(this);
		var del_id = element.attr("id");
		var url = sites +"/delete_item/"+ del_id;
		$(".error").fadeOut();
		
		// batas
		$.ajax({
			type: 'POST',
			url: url,
    	    cache: false,
			headers: { "cache-control": "no-cache" },
			success: function(result) {
				
				res = result.split("|");
				if (res[0] == "true")
				{   error_mess(1,res[1],0);
					location.reload();
				}
				else if (res[0] == 'warning'){ error_mess(2,res[1],0); }
				else{ error_mess(3,res[1],0); }
			}
		})
		return false;	
	});

	// get qty kontrak from api
	$('#bgetqty').click(function() 
	{
		var url = sites+"/get_out_standing/";
		var value = $("#titem").val();
		var nilai = '{ "origin":"'+value+'"}';
		// console.log(nilai);

		// console.log(url);
			
		$.ajax({
			type: 'POST',
			url: url,
			data: nilai,
			contentType: "application/json",
            dataType: 'json',
			success: function(data){
				// console.log("device control succeeded");
				console.log(data.amount); 
				$("#tcontractqty").val(data.amount);
				$("#toustandingqty").val(data.oustanding);
				$("#ttransferqty").val(data.oustanding);
			},
			error: function(e){
				console.log("Error posting data");
			}
		});
	});


	// delete item sounding
    $(document).on('click','.text-remove-sounding',function(e)
	{	
		e.preventDefault();
		var element = $(this);
		var del_id = element.attr("id");
		var url = sites +"/delete_item_sounding/"+ del_id;
		$(".error").fadeOut();

		// console.log(url);
		
		// batas
		$.ajax({
			type: 'POST',
			url: url,
    	    cache: false,
			headers: { "cache-control": "no-cache" },
			success: function(result) {
				
				res = result.split("|");
				if (res[0] == "true")
				{   error_mess(1,res[1],0);
					location.reload();
				}
				else if (res[0] == 'warning'){ error_mess(2,res[1],0); }
				else{ error_mess(3,res[1],0); }
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
		var param = ['searching',date,cust];
		
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
				load_data_search(param);
			}
		})
		return false;
		swal('Error Load Data...!', "", "error");
		
	});

		// fungsi ajax form sales
		$('#ajaxtransform,#ajaxtransform1,#ajaxtransform2').submit(function() {

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
						// location.reload();
						// console.log(res[1]);
						error_mess(1,res[1],0);
						setTimeout(location.reload(), 1500);
						
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
				url: source+"/"+search[0]+"/"+search[1]+"/"+search[2],
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
						'CO-0'+s[i][0],
						s[i][1],
						s[i][2],
						s[i][3],
						s[i][4]+' - '+s[i][5],
						s[i][7],
						s[i][8],
						s[i][9],
						
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
						  if (s[i][6] == 1){ stts = 'btn btn-success'; }else { stts = 'btn btn-danger'; }
						  oTable.fnAddData([
        '<input type="checkbox" class="cek" name="cek[]" value="'+s[i][0]+'" id="cek'+i+'" style="margin:0px"  />',
										i+1,
										s[i][4],
										s[i][1],
										s[i][2],
										s[i][3],
										s[i][5]+'<br/>'+s[i][6],
										s[i][7]+'<br/>'+s[i][8],
										
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
	