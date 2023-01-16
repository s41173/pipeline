$(document).ready(function (e) {
	
	// get api id
	$(document).on('click','#bfetchapi',function(e)
	{	
		e.preventDefault();

		// var url = sites_attribute +"/"+ del_id;
		// $.ajax({
		// 	type: 'POST',
		// 	url: sites_density_remove+"/"+del_id,
    // 	cache: false,
		// 	headers: { "cache-control": "no-cache" },
		// 	success: function(result) {
				
		// 		res = result.split("|");
		// 		if (res[0] == 'true'){ load_density(); 
		// 			swal({ title: res[1], type: 'warning', timer: 2000, showConfirmButton:false});
		// 		}
		// 	}
		// })
		// return false;	

	});


	// ====================================== cincin function ========================================
	
	$('#ajaxformcincin,#ajaxformcincin1').submit(function() {
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
						error_mess(1,res[1]);
						$("#bresetcincin").click();
						load_cincin();
				}
				else{ error_mess(3,res[1]); }
			},
			error: function(e) 
	    	{
				$("#error").html(e).fadeIn();
				console.log(e.responseText);	
	    	} 
		})
		return false;
	});

	$(document).on('click','.text-remove-cincin',function(e)
	{	
		e.preventDefault();
		var element = $(this);
		var del_id = element.attr("id");

		$.ajax({
			type: 'POST',
			url: sites_cincin_remove+"/"+del_id,
    	cache: false,
			headers: { "cache-control": "no-cache" },
			success: function(result) {
				
				res = result.split("|");
				if (res[0] == 'true'){ load_cincin(); 
					swal({ title: res[1], type: 'warning', timer: 2000, showConfirmButton:false});
				}
			}
		})
		return false;	

	});

	// change combotype
	$(document).on('change','#cpress_type',function(e)
	{	
		e.preventDefault();
		var val = $(this).val();

		if (val>0){
			$('.tpressval').prop('readonly', true);
      $('#t'+val).prop('readonly', false);
		}else{ $('.tpressval').prop('readonly', true); }
	});

	// change presisi by id
	$(document).on('click','#bpresisi',function(e)
	{	
		e.preventDefault();
		var val = $("#cpress_type").val();
		if (val > 0){
      
			var values = $("#t"+val).val();
			var presisi = $("#press"+val).val();
			var cincinid = $("#tankidcincin").val();
			
			$.ajax({
				type: 'POST',
				url: sites_presisi_update+"/"+cincinid+"/"+presisi+"/"+values,
				cache: false,
				headers: { "cache-control": "no-cache" },
				success: function(result) {
					
					res = result.split("|");
					if (res[0] == 'true'){ $('.tpressval').prop('readonly', true);
						swal({ title: res[1], type: 'success', timer: 2000, showConfirmButton:false});
					}else{ swal({ title: res[1], type: 'error', timer: 2000, showConfirmButton:false}); }
				}
			})
			return false;	

		}else{ swal({ title: 'Invalid Precision', type: 'error', timer: 2000, showConfirmButton:false}); }
		
		
	});

		// update kalibrasi
		$(document).on('click','.text-update-cincin',function(e)
		{	
			e.preventDefault();
			var element = $(this);
			var del_id = element.attr("id");
			var url = sites_cincin_update +"/"+ del_id;
	
			$(".error").fadeOut();
	
			// batas
			$.ajax({
				type: 'POST',
				url: url,
				cache: false,
				headers: { "cache-control": "no-cache" },
				success: function(result) {
					
					res = result.split(":");
					res1 = res[0].split("|");
					res2 = res[1].split("|");

					if (res1[0] == 'true'){
						$("#myModal6").modal('show');
						$("#tankidcincin").val(del_id);

						$("#tringno").val(res1[1]);
						$("#tstart").val(res1[2]);
						$("#tend").val(res1[3]);

			for(var i = 0; i < res2.length-1; i++) {
				
				var x = i+1;
				presisi = res2[i].split('-');
				$("#t"+x).val(presisi[2]);
				$("#press"+x).val(presisi[0]);
			}

					}
				}
			})
			return false;	
		});

	// ====================================== calibrate function ========================================
	
	$('#ajaxformcalibrate,#ajaxformcalibrate1').submit(function() {
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
						error_mess(1,res[1]);
						$("#bresetcalibrasi").click();
						load_calibrate();
				}
				else{ error_mess(3,res[1]); }
			},
			error: function(e) 
	    	{
				$("#error").html(e).fadeIn();
				console.log(e.responseText);	
	    	} 
		})
		return false;
	});

	$(document).on('click','.text-remove-calibrate',function(e)
	{	
		e.preventDefault();
		var element = $(this);
		var del_id = element.attr("id");

		$.ajax({
			type: 'POST',
			url: sites_calibrate_remove+"/"+del_id,
    	cache: false,
			headers: { "cache-control": "no-cache" },
			success: function(result) {
				
				res = result.split("|");
				if (res[0] == 'true'){ load_calibrate(); 
					swal({ title: res[1], type: 'warning', timer: 2000, showConfirmButton:false});
				}
			}
		})
		return false;	

	});

	// update kalibrasi
	$(document).on('click','.text-update-density',function(e)
	{	
		e.preventDefault();
		var element = $(this);
		var del_id = element.attr("id");
		var url = sites_density_update +"/"+ del_id;

		$(".error").fadeOut();

		// batas
		$.ajax({
			type: 'POST',
			url: url,
    	cache: false,
			headers: { "cache-control": "no-cache" },
			success: function(result) {
				
				res = result.split("|");
				if (res[0] == 'true'){
					$("#myModal1").modal('show');
					$("#tankid").val(del_id);
					$("#ttanksuhu").val(res[1]);
				  $("#ttankdensity").val(res[2]);
				}
			}
		})
		return false;	
	});



	// ====================================== calibration function ========================================

		// fungsi update density
	$(document).on('click','.text-update-calibrate',function(e)
	{	
		e.preventDefault();
		var element = $(this);
		var del_id = element.attr("id");
		var url = sites_calibrate_update +"/"+ del_id;

		$(".error").fadeOut();
		// batas
		$.ajax({
			type: 'POST',
			url: url,
    	cache: false,
			headers: { "cache-control": "no-cache" },
			success: function(result) {

				res = result.split("|");
				if (res[0] == 'true'){
					$("#myModal4").modal('show');
					$("#tankidcalibration").val(del_id);
					$("#theightcalibration").val(Math.round(parseFloat(res[1]*100)));
				  $("#tvolume").val(res[2]);
				}
			}
		})
		return false;	
	});

	$(document).on('click','.text-remove-density',function(e)
	{	
		e.preventDefault();
		var element = $(this);
		var del_id = element.attr("id");

		// var url = sites_attribute +"/"+ del_id;
		$.ajax({
			type: 'POST',
			url: sites_density_remove+"/"+del_id,
    	cache: false,
			headers: { "cache-control": "no-cache" },
			success: function(result) {
				
				res = result.split("|");
				if (res[0] == 'true'){ load_density(); 
					swal({ title: res[1], type: 'warning', timer: 2000, showConfirmButton:false});
				}
			}
		})
		return false;	

	});


	$('#ajaxformdensity,#ajaxformdensity1').submit(function() {
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
						error_mess(1,res[1]);
						$("#breset").click();
						load_density();
				}
				else{ error_mess(3,res[1]); }
			},
			error: function(e) 
	    	{
				$("#error").html(e).fadeIn();
				console.log(e.responseText);	
	    	} 
		})
		return false;
	});

// ====================================== density function ========================================
		
// document ready end	
});

	// function get calibrate
	function load_calibrate()
	{
		$(document).ready(function (e) {
			
			var oTable = $('#table-calibrate');
			var hasil = null;
			var height = 0;

		    $.ajax({
				type : 'GET',
				// url: 'https://lit-journey-84438.herokuapp.com/api/bulking/kalibrasi/5cc27d3cf24b0e0017ff901c',
				url: url_calibrate,
				//force to handle it as text
				contentType: "application/json",
				dataType: "json",
				success: function(result) 
				{   
						// oTable.append("<tr> <td> 32.00 </td> <td> 0.92745920 </td> </tr>");

							for(var i = 0; i < result.length; i++) {
								height = Math.round(parseFloat(result[i].h*100));
								
hasil = hasil+"<tr> <td>"+height+"</td> <td>"+formatNumber(result[i].V)+"</td>"+
'<td> <div class="btn-group" role"group">'+
'<a class="btn btn-primary btn-xs text-update-calibrate" id="' +result[i]._id+ '" title=""> <i class="fa fas-2x fa-edit"> </i> </a> '+
'<a class="btn btn-danger btn-xs text-remove-calibrate" id="'+result[i]._id+'" title="delete"> <i class="fa fas-2x fa-trash"> </i> </a>'+
'</div> </td>'+"</tr>";			

							} // End For 
							oTable.html("");
							oTable.html(hasil);
				},
				error: function(e){
				   oTable.html("");  
				   console.log(e.responseText);	
				}
				
			});  // ajax end
			
        });// end document ready	
	} // end get calibrate



	// function get density
	function load_density()
	{
		$(document).ready(function (e) {

			var oTable = $('#table-density');
			var hasil = null;
			// oTable.html("");

		    $.ajax({
				type : 'GET',
				url: url_density,
				//force to handle it as text
				contentType: "application/json",
				dataType: "json",
				success: function(result) 
				{   
							for(var i = 0; i < result.length; i++) {
								
hasil = hasil+"<tr> <td>"+result[i].suhu+"</td> <td>"+result[i].nilai_densitas+"</td>"+
'<td> <div class="btn-group" role"group">'+
'<a class="btn btn-primary btn-xs text-update-density" id="' +result[i]._id+ '" title=""> <i class="fa fas-2x fa-edit"> </i> </a> '+
'<a class="btn btn-danger btn-xs text-remove-density" id="'+result[i]._id+'" title="delete"> <i class="fa fas-2x fa-trash"> </i> </a>'+
'</div> </td>'+"</tr>";			

							} // End For 
							oTable.html("");
							oTable.html(hasil);		
							load_calibrate();		
							load_cincin();
				},
				error: function(e){
				   oTable.html("");  
					 console.log(e.responseText);	
				}
				
			});  // ajax end
			
				});// end document ready	
				
	} // end get density
	

		// function get cincin
	function load_cincin()
		{
			$(document).ready(function (e) {
	
				var oTable = $('#table-cincin');
				var hasil = null;
	
					$.ajax({
					type : 'GET',
					url: url_cincin,
					//force to handle it as text
					contentType: "application/json",
					dataType: "json",
					success: function(result) 
					{   
								for(var i = 0; i < result.length; i++) {

							//		get_presisi(result[i].precision);
									
									
	hasil = hasil+"<tr> <td>"+result[i].ringNo+"</td> <td>"+result[i].h_end+"</td>"+get_presisi(result[i].precision)+
	'<td> <div class="btn-group" role"group">'+
	'<a class="btn btn-primary btn-xs text-update-cincin" id="' +result[i]._id+ '" title=""> <i class="fa fas-2x fa-edit"> </i> </a> '+
	'<a class="btn btn-danger btn-xs text-remove-cincin" id="'+result[i]._id+'" title="delete"> <i class="fa fas-2x fa-trash"> </i> </a>'+
	'</div> </td>'+"</tr>";			
	
								} // End For 
								oTable.html("");
								oTable.html(hasil);			
					},
					error: function(e){
						 oTable.html("");  
						 console.log(e.responseText);	
					}
					
				});  // ajax end
				
					});// end document ready	
					
		} // end get cincin

		function get_presisi(presisi){

			  var hasil = null;
			  for(var i = 0; i < presisi.length; i++){
					// console.log(presisi[i].V);
					hasil = hasil+"<td>"+presisi[i].V+"</td>";
				}
				return hasil;
		}