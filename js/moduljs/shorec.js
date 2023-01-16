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

	 $('#dtime1').daterangepicker({
		autoUpdateInput: false,
		timePicker: true,
		singleDatePicker: true,
		showDropdowns: true,
		timePicker24Hour: true,
		locale: { format: 'YYYY/MM/DD H:mm'}
	}, function(chosen_date) {
		$('#dtime1').val(chosen_date.format('YYYY/MM/DD H:mm'));
	});

	$('#dtime2').daterangepicker({
		autoUpdateInput: false,
		timePicker: true,
		singleDatePicker: true,
		showDropdowns: true,
		timePicker24Hour: true,
		locale: { format: 'YYYY/MM/DD H:mm'}
	}, function(chosen_date) {
		$('#dtime2').val(chosen_date.format('YYYY/MM/DD H:mm'));
	});

	$('#dtime3').daterangepicker({
		autoUpdateInput: false,
		timePicker: true,
		singleDatePicker: true,
		showDropdowns: true,
		timePicker24Hour: true,
		locale: { format: 'YYYY/MM/DD H:mm'}
	}, function(chosen_date) {
		$('#dtime3').val(chosen_date.format('YYYY/MM/DD H:mm'));
	});
	
	$('#dtime4').daterangepicker({
		autoUpdateInput: false,
		timePicker: true,
		singleDatePicker: true,
		showDropdowns: true,
		timePicker24Hour: true,
		locale: { format: 'YYYY/MM/DD H:mm'}
	}, function(chosen_date) {
		$('#dtime4').val(chosen_date.format('YYYY/MM/DD H:mm'));
	});

	$('#dtime5').daterangepicker({
		autoUpdateInput: false,
		timePicker: true,
		singleDatePicker: true,
		showDropdowns: true,
		timePicker24Hour: true,
		locale: { format: 'YYYY/MM/DD H:mm'}
	}, function(chosen_date) {
		$('#dtime5').val(chosen_date.format('YYYY/MM/DD H:mm'));
	});

	$('#dtime6').daterangepicker({
		autoUpdateInput: false,
		timePicker: true,
		singleDatePicker: true,
		showDropdowns: true,
		timePicker24Hour: true,
		locale: { format: 'YYYY/MM/DD H:mm'}
	}, function(chosen_date) {
		$('#dtime6').val(chosen_date.format('YYYY/MM/DD H:mm'));
	});

	$('#dtime7').daterangepicker({
		autoUpdateInput: false,
		timePicker: true,
		singleDatePicker: true,
		showDropdowns: true,
		timePicker24Hour: true,
		locale: { format: 'YYYY/MM/DD H:mm'}
	}, function(chosen_date) {
		$('#dtime7').val(chosen_date.format('YYYY/MM/DD H:mm'));
	});

	$('#dtime8').daterangepicker({
		autoUpdateInput: false,
		timePicker: true,
		singleDatePicker: true,
		showDropdowns: true,
		timePicker24Hour: true,
		locale: { format: 'YYYY/MM/DD H:mm'}
	}, function(chosen_date) {
		$('#dtime8').val(chosen_date.format('YYYY/MM/DD H:mm'));
	});
	
//   date range picker

$(document).on('keyup','#tincm_input,#tincm_output,#tcorcm_input,#tcorcm_output',function(e)
{	
	e.preventDefault();
	var elem = $(this);

	if (elem.attr('id') == "tcorcm_input" || elem.attr('id') == "tincm_input"){ 
		var input = $("#tincm_input").val();
		var corr = $("#tcorcm_input").val();
		var output = $("#tacorr_input");
	}else if(elem.attr('id') == "tcorcm_output" || elem.attr('id') == "tincm_output"){
		var input = $("#tincm_output").val();
		var corr = $("#tcorcm_output").val();
		var output = $("#tacorr_output");
	}

	var res = parseFloat(input)+parseFloat(corr);
	if (isNaN(res)){ output.val('0'); }else{ output.val(res); }
});

$(document).on('keyup','#ttemp_input',function(e)
{	
	e.preventDefault();
	$("#ttemp_output").val($(this).val());
});

// sounding before and after function
$(document).on('click','#bfetchtank',function(e)
{	
	e.preventDefault();
	$("#bsubmitsounding").prop('disabled', true);
	$(this).prop('disabled', true);

	var tank = $("#csource").val();
	var input_before = $("#tincm_input").val();
	var input_after = $("#tincm_output").val();
	var sounding_before = $("#tacorr_input").val();
	var sounding_after = $("#tacorr_output").val();
	var temp_before = $("#ttemp_input").val();
	var temp_after = $("#ttemp_output").val();
	var mess = null;

	if (temp_after == "" || temp_after == 0){ mess = 'After - Temperature Value Required';  }
	if (temp_before == "" || temp_before == 0){ mess = 'Before - Temperature Value Required';  }
	if (sounding_after == "" || sounding_after == 0){ mess = 'After - Correction Sounding Value Required';  }
	if (sounding_before == "" || sounding_before == 0){ mess = 'Before - Correction Sounding Value Required';  }
	if (input_after == "" || input_after == 0){ mess = 'After - Sounding Value Required';  }
	if (input_before == "" || input_before == 0){ mess = 'Before - Sounding Value Required';  }

	if (!mess){
		
		get_density(tank,temp_before,temp_after);

	}else{ error_mess(3,mess,0); }

});


	load_data();  

	// transaction type
	$(document).on('change','#ctype',function(e)
	{	
		e.preventDefault();
		var elem = $(this).val();
		if (elem == 0){ $("#cvessel").prop('disabled', false); }
		else if (elem == "nol"){ $("#cvessel").prop('disabled', true); $("#csource").prop('disabled', true); }
		else{ $("#cvessel").prop('disabled', true); $("#csource").prop('disabled', false); }
	
	});

	$(document).on('change','#csource',function(e)
	{	
		e.preventDefault();
		var elem = $(this).val();
		// console.log(sites+"/get_tank_details/"+elem+"/content");
		
		if (elem){
			$.ajax({
				type: 'POST',
				url: sites+"/get_tank_details/"+elem+"/content",
				cache: false,
				headers: { "cache-control": "no-cache" },
				success: function(result) {
					$("#tcontent").val(result);
				}
			})
			return false;
		}
		
	});

	// calculate correction
	$(document).on('keyup','#tadj_input,#tadj_output',function(e)
	{	
		var elem = $(this);
		e.preventDefault();

		if (elem.attr('id') == "tadj_input"){ 

			var hnetkg = $("#hnetkg_input");
			var netkg = parseFloat($("#hnetkg_input").val());
			var adj = parseFloat($(this).val());
			var begin = parseFloat($("#hbegin_input").val());
			var coeff = parseFloat($("#tcoeff_input").val());
			var tnetkg = $("#tnetkg_input");
			var metric = $("#tmetricton_input");

		}else if (elem.attr('id') == "tadj_output"){
			var hnetkg = $("#hnetkg_output");
			var netkg = parseFloat($("#hnetkg_output").val());
			var adj = parseFloat($(this).val());
			var begin = parseFloat($("#hbegin_output").val());
			var coeff = parseFloat($("#tcoeff_output").val());
			var tnetkg = $("#tnetkg_output");
			var metric = $("#tmetricton_output");
		}

		key = e.which || e.keyCode || e.charCode;
		if (key != 8 && key != 187 && key != 189 && key != 190){
			if (netkg != '0' && netkg != "" && netkg != 0){

				if (adj == 0 || adj == ""){
					hnetkg.val(begin);
					$(this).val(0);
					tnetkg.val(formatNumber(begin));
					metric.val("");

				}else{
					hasil = netkg+adj;
					hnetkg.val(begin);
					tnetkg.val(formatNumber(hasil));
					metric.val(formatNumber(Math.round(hasil*coeff)));
				}
			}
		}
		
	});


	// checkbox set empty 

	// form submit update
		// ajax form non upload data
	$("#upload_form_update,#upload_form_update2,#upload_form_update3").on('submit',(function(e) {
		
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
	$('#soundingform').submit(function() {
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
					  // location.reload(true);
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
		var url = sites_invoice +"/"+ del_id;
		
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

	// execution status
	$(document).on('click','.run_status',function(e)
	{	
		e.preventDefault();
		var element = $(this);
		var del_id = element.attr("id");
		var url = sites_execution +"/"+ del_id;
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

function get_density(tank,temp_before,temp_after){

		$.ajax({
			type: 'POST',
			url: sites_sounding+"/get_density/"+tank+"/"+temp_before,
			cache: false,
			headers: { "cache-control": "no-cache" },
			success: function(result) {
				
				res = result.split("|");
				if (res[0]== 'true'){ 
					$("#tdensity_input").val(res[1]); $("#tcoeff_input").val(res[2]); 
					get_density_after(tank,temp_after);
				// 	get_sounding();
				}else{ error_mess(2,res[1],0); $("#breset").click(); }
			}
		})
		return false;
}

function get_density_after(tank,temp){

	 $.ajax({
		 type: 'POST',
		 url: sites_sounding+"/get_density/"+tank+"/"+temp,
		 cache: false,
		 headers: { "cache-control": "no-cache" },
		 success: function(result) {
			 
			 res = result.split("|");
			 if (res[0]== 'true'){ 
				$("#tdensity_output").val(res[1]); $("#tcoeff_output").val(res[2]); 
			 	get_sounding(tank,0);
			 }else{ error_mess(2,res[1],0); $("#breset").click(); }
		 }
	 })
	 return false;
}

function get_sounding(tank,type=0){

	$(document).ready(function (e) {
		
		if (type == 0){
			var height = $("#tacorr_input").val();
			var temp = $("#ttemp_input").val();
			var netkg = $("#tnetkg_input");
			var obv = $("#tobv_input");
			var hobv = $("#hobv_input");
			var hnetkg = $("#hnetkg_input");
			var hbegin = $("#hbegin_input");
		}else if (type == 1){
			var height = $("#tacorr_output").val();
			var temp = $("#ttemp_output").val();
			var netkg = $("#tnetkg_output");
			var obv = $("#tobv_output");
			var hobv = $("#hobv_output");
			var hnetkg = $("#hnetkg_output");
			var hbegin = $("#hbegin_output");
		}

		$.ajax({
			type: 'POST',
			url: sites_sounding+"/get_massa/"+tank+"/"+height+"/"+temp,
			cache: false,
			headers: { "cache-control": "no-cache" },
			success: function(result) {

				res = result.split("|");
				if (res[0] == "true"){ 
					hnetkg.val(res[1]); 
					hbegin.val(res[1]); 
					netkg.val(formatNumber(res[1])); 
					hobv.val(res[2]); 
					obv.val(formatNumber(res[2]));  
					if (type == 0){ get_sounding(tank,1); }else if(type == 1){ $("#bsubmitsounding,#bfetchtank").prop('disabled', false);  }
				}
				else{ netkg.val('0'); obv.val('0');  error_mess(3,res[1],0); $("#breset").click(); }
			}
		})
		return false;	
	});
}

	function load_data_search(search=null)
	{
		$(document).ready(function (e) {
			
			var oTable = $('#datatable-buttons').dataTable();
			var stts = 'btn btn-danger';
			var stts1 = 'btn btn-danger';
			
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
			if (s[i][8] == 1){ stts1 = 'btn btn-success'; }else { stts1 = 'btn btn-danger'; }
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
'<a href="" class="'+stts1+' btn-xs run_status" id="' +s[i][0]+ '" title="Closing Status"> <i class="fa fa-check-circle"> </i> </a> '+
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
												s[i][9],
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
			var stts1 = 'btn btn-danger';
			
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
							if (s[i][8] == 1){ stts1 = 'btn btn-success'; }else { stts1 = 'btn btn-danger'; }
						  oTable.fnAddData([
        '<input type="checkbox" class="cek" name="cek[]" value="'+s[i][0]+'" id="cek'+i+'" style="margin:0px"  />',
										i+1,
										s[i][1],
										s[i][2],
										s[i][4],
										s[i][3],
										s[i][9],
										
'<div class="btn-group" role"group">'+
'<a href="" class="'+stts+' btn-xs primary_status" id="' +s[i][0]+ '" title="Primary Status"> <i class="fa fa-power-off"> </i> </a> '+
'<a href="" class="'+stts1+' btn-xs run_status" id="' +s[i][0]+ '" title="Closing Status"> <i class="fa fa-check-circle"> </i> </a> '+
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

			// console.log(ck);
			if($("#"+ck).prop("checked") == true){
				$("#"+target).prop('disabled', false); 
			}
			else if($("#"+ck).prop("checked") == false){
					$("#"+target).val("");
					$("#"+target).prop('disabled', true); 
			}
		})
	}	