$(document).ready(function (e) {
	
    // function general
	
	$('#datatable-buttons').dataTable({
	 dom: 'T<"clear">lfrtip',
		tableTools: {"sSwfPath": site}
	 });
	 
	// date time picker
	$('#d1,#d2,#d3,#d4,#d5').daterangepicker({
		 locale: {format: 'YYYY/MM/DD'}
    }); 
	
    $('#ds1,#ds2').daterangepicker({
        locale: {format: 'YYYY-MM-DD'},
		singleDatePicker: true,
        showDropdowns: true
     });
	
	load_data();  
	
	// batas dtatable
	
	// fungsi jquery update
	$(document).on('click','.text-primary',function(e)
	{	
		e.preventDefault();
		var element = $(this);
		var del_id = element.attr("id");
		var url = sites_ajax +"/update/"+ del_id;
		
		window.location.href = url;
	});

	// fungsi jquery ledger
	$(document).on('click','.text-invoice',function(e)
	{	
		e.preventDefault();
		var element = $(this);
		var del_id = element.attr("id");
		var url = sites_ajax +"/invoice/"+ del_id;
		window.open(url, "_blank", "scrollbars=1,resizable=0,height=600,width=800");
	});

	// calculate correction
	$(document).on('keyup','#tadj',function(e)
	{	
		e.preventDefault();
		var netkg = parseFloat($("#hnetkg").val());
		var adj = parseFloat($(this).val());
		var begin = parseFloat($("#hbegin").val());
		var coeff = parseFloat($("#tcoeff").val());

		key = e.which || e.keyCode || e.charCode;
		if (key != 8 && key != 187 && key != 189 && key != 190){
			if (netkg != '0' && netkg != "" && netkg != 0){

				if (adj == 0 || adj == ""){
					$("#hnetkg").val(begin);
					$(this).val(0);
					$("#tnetkg").val(formatNumber(begin));
					$("#tmetricton").val("");
				}else{
					hasil = netkg+adj;
			   	$("#hnetkg").val(hasil);
					$("#tnetkg").val(formatNumber(hasil));
					$("#tmetricton").val(formatNumber(Math.round(hasil*coeff)));
				}
			}
		}
	});

	$(document).on('click','#breset',function(e)
	{	
		e.preventDefault();
		$("#hnetkg,#hobv").val('0');
		// $('#journalformdata').reset();
		$('#journalformdata').trigger("reset");
	});

		// adjustment kg
		$(document).on('keyup','#tincm,#tcorcm',function(e)
		{	
			e.preventDefault();
			var input = $("#tincm").val();
			var corr = $("#tcorcm").val();
			var res = parseFloat(input)+parseFloat(corr);
			if (isNaN(res)){ $("#tacorr").val('0'); }else{ $("#tacorr").val(res); }
		});

	// tank sounding
	$(document).on('click','#bfetchtank',function(e)
	{	
		e.preventDefault();
		var tank = $("#ctank").val();
		var input = $("#tincm").val();
		var sounding = $("#tacorr").val();
		var temp = $("#ttemp").val();
		var mess = null;

		if (input == "" || input == 0){ mess = 'Sounding Value Required';  }
		if (sounding == "" || sounding == 0){ mess = 'Correction Sounding Value Required';  }
		if (temp == "" || temp == 0){ mess = 'Temperature Value Required';  }

		if (!mess){
			// batas
			$.ajax({
				type: 'POST',
				url: sites+"/get_density/"+tank+"/"+temp,
				cache: false,
				headers: { "cache-control": "no-cache" },
				success: function(result) {
					
					res = result.split("|");
					if (res[0]== 'true'){ 
						$("#tdensity").val(res[1]); $("#tcoeff").val(res[2]); 
						get_sounding();
				
				  }else{ error_mess(2,res[1],0); }
				}
			})
			return false;

		}else{ error_mess(3,mess,0); }
	});

	$(document).on('change','#ctank',function(e)
	{	
		e.preventDefault();
		$("#tcorcm").val('0');
		$("#ttemp").val('0');
		$("#tcoeff").val('0');
		$("#tdensity").val('0');
		$("#tnetkg").val('0');
		$("#hnetkg,#hobv,#hbegin").val('0');
	});
	
	// publish status
	$(document).on('click','.primary_status',function(e)
	{	
		e.preventDefault();
		var element = $(this);
		var del_id = element.attr("id");
		var url = sites_ajax +"/confirmation/"+ del_id;
		$(".error").fadeOut();
		
		// // $("#myModal2").modal('show');
		// // batas
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

	// gl transaction delete
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

	// fungsi ajax counter
	$(document).on('change','#ctype',function(e)
	{	
		e.preventDefault();
		var value = $(this).val();
		var url = sites+'/counter/'+value+'/ajax';

		$.ajax({
			type: 'POST',
			url: url,
    	    data: "value="+ value,
			success: function(result) {
			  $("#tno,#tno_update").val(result);
			}
		})
		return false;	
	});

	// fungsi ajax transform
	$('#ajaxtransform').submit(function() {

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
					setTimeout(function() { location.reload(); }, 3500);
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
	
	
	$('#searchform').submit(function() {
		
		var no = $("#tno").val();
		var dates = $("#ds1").val();
		var param = ['searching',no,dates];
		
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
		
// document ready end	
});

   // get sounding
	function get_sounding(){

			$(document).ready(function (e) {
				
				var tank = $("#ctank").val();
				var height = $("#tacorr").val();
				var temp = $("#ttemp").val();
	
				$.ajax({
					type: 'POST',
					url: sites+"/get_massa/"+tank+"/"+height+"/"+temp,
					cache: false,
					headers: { "cache-control": "no-cache" },
					success: function(result) {

						res = result.split("|");
						if (res[0] == "true"){ $("#hnetkg,#hbegin").val(res[1]); $("#tnetkg").val(formatNumber(res[1])); $("#hobv").val(res[2]); $("#tobv").val(formatNumber(res[2]));  }
						else{ $("#tnetkg").val('0');  error_mess(3,res[1],0); }
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
			
	//		console.log(source+"/"+search[0]+"/"+search[1]+"/"+search[2]);

		    $.ajax({
				type : 'GET',
				url: source+"/"+search[0]+"/"+search[1]+"/"+search[2],
				//force to handle it as text
				contentType: "application/json",
				dataType: "json",
				success: function(s) 
				{   
				   //    console.log(s);
					  
						oTable.fnClearTable();
						$(".chkselect").remove()
	
		$("#chkbox").append('<input type="checkbox" name="newsletter" value="accept1" onclick="cekall('+s.length+')" id="chkselect" class="chkselect">');
							
		for(var i = 0; i < s.length; i++) {
			if (s[i][7] == 1){ stts = 'btn btn-success'; }else { stts = 'btn btn-danger'; }	
			oTable.fnAddData([
'<input type="checkbox" name="cek[]" value="'+s[i][0]+'" id="cek'+i+'" style="margin:0px"  />',
						i+1,
						s[i][1],
						s[i][3],
						s[i][2],
						s[i][6],
'<div class="btn-group" role"group">'+
'<a href="" class="'+stts+' btn-xs primary_status" id="' +s[i][0]+ '" title="Primary Status"> <i class="fa fa-power-off"> </i> </a> '+
'<a href="" class="btn btn-warning btn-xs text-invoice" id="' +s[i][0]+ '" title=""> <i class="fa fas-2x fa-book"> </i> </a>'+
// '<a href="" class="btn btn-warning btn-xs text-balance" id="' +s[i][0]+ '" title=""> <i class="fa fas-2x fa-money"> </i> </a>'+
'<a href="" class="btn btn-primary btn-xs text-primary" id="' +s[i][0]+ '" title=""> <i class="fa fas-2x fa-edit"> </i> </a>'+
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
				      // console.log(s);
					  
						oTable.fnClearTable();
						$(".chkselect").remove()
	
		$("#chkbox").append('<input type="checkbox" name="newsletter" value="accept1" onclick="cekall('+s.length+')" id="chkselect" class="chkselect">');
							
							for(var i = 0; i < s.length; i++) {
						  if (s[i][7] == 1){ stts = 'btn btn-success'; }else { stts = 'btn btn-danger'; }	
						  oTable.fnAddData([
'<input type="checkbox" name="cek[]" value="'+s[i][0]+'" id="cek'+i+'" style="margin:0px"  />',
										i+1,
										s[i][1],
										s[i][3],
										s[i][2],
										s[i][6],
'<div class="btn-group" role"group">'+
'<a href="" class="'+stts+' btn-xs primary_status" id="' +s[i][0]+ '" title="Primary Status"> <i class="fa fa-power-off"> </i> </a> '+
'<a href="" class="btn btn-warning btn-xs text-invoice" id="' +s[i][0]+ '" title=""> <i class="fa fas-2x fa-book"> </i> </a>'+
// '<a href="" class="btn btn-warning btn-xs text-balance" id="' +s[i][0]+ '" title=""> <i class="fa fas-2x fa-money"> </i> </a>'+
'<a href="" class="btn btn-primary btn-xs text-primary" id="' +s[i][0]+ '" title=""> <i class="fa fas-2x fa-edit"> </i> </a>'+
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
	
	// batas fungsi load data
	function resets()
	{  
	   $(document).ready(function (e) {
		  // reset form
		  $("#tcode, #tno, #tname, #talias").val("");
		  $("#cclassi option:selected").prop("selected", false);
		  $("#ccur option:selected").prop("selected", false);
		  $("#cactive option:checked").prop("selected", false);
		  $("#cbank option:checked").prop("selected", false);
	  });
	}
	
