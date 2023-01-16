<!DOCTYPE html>
<html lang="en" >
<head>
  <meta charset="UTF-8">
  <link rel="shortcut icon" href="<?php echo base_url().'images/fav_icon.png';?>" />
  <title> Login </title>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.6 -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css" integrity="sha384-/Y6pD6FV/Vv2HJnA6t+vslU6fwYXjCFtcEpHbNJ0lyAFsXTsjBbfaDjzALeQsN6M" crossorigin="anonymous">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">

    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet"> <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0-beta/css/bootstrap.min.css'>
<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css'>
<link rel='stylesheet' href='https://fonts.googleapis.com/css?family=Roboto'>
<style type="text/css">@import url("<?php echo base_url().'login_file/style.css'; ?>");</style>
<script type="text/javascript" src="<?php echo base_url().'login_file/js/jquery.js'; ?>"></script>
    <!-- sweet alert js -->
    <script type="text/javascript" src="<?php echo base_url().'login_file/js/sweetalert.js'; ?>"></script>
    <style type="text/css">@import url("<?php echo base_url() . 'login_file/css/sweetalert.css'; ?>");</style>
    
<script type="text/javascript">
    
// function cek login
	function valid_login()
	{
	  $(document).ready(function (e) {
		  
        var cek_login = "<?php echo site_url('login/cek_login'); ?>";  
		// batas
		$.ajax({
			type: 'POST',
			url: cek_login,
    	    cache: false,
			headers: { "cache-control": "no-cache" },
			success: function(result) {
				
				if (result == 'false'){ 
					window.close();
				}
			}
		})
		return false;	
	   
	   // document ready end	
      });
	}

$(document).ready(function (e) {
	
	
	$('#user,#pass').keypress(function (e) {
	 var key = e.which;
	 if(key == 13)  // the enter key code
	  {
        $('#loginbutton').click(); 
	  }
	});   
	
	$('#loginbutton').click(function() 
	{
		var user = $("#user").val();
		var pass = $("#pass").val();
		
		if (user != "" && pass != "")
		{
			var nilai = '{ "user":"'+user+'", "pass": "'+pass+'"}';
				
			$.ajax({
				type: 'POST',
                url: '<?php echo site_url('login/login_process'); ?>',
				data : nilai,
			    contentType: "application/json",
                dataType: 'json',
				success: function(data) 
			    {
                    console.log(data);
					if (data.Success == true){ 
                        if (data.Info != null){
                                                        
                            swal({
                              title: data.Info,text: "Contact your administrator", icon: "warning", buttons: false, showConfirmButton:false,
                            })

                            setTimeout(function(){ window.location = "<?php echo site_url('main'); ?>"; }, 3000);
                        }else{
                            window.location = "<?php echo site_url('main'); ?>";   
                        }
                    }
					else{ swal(data.Info, "", "error"); $("#user,#pass").val(''); $("#user").focus(); }
				}
			}) 
			return false;
			
		}
		else{ $("#user").val(''); $("#pass").val(''); swal("Invalid Username Or Password..!!", "", "error");  }
		
	});


// document ready end	
});

</script>
    
</head>
<body>
<!-- partial:index.partial.html -->
<div class="container">
        <div class="row">
            <div class="col-lg-3 col-md-2"></div>
            <div class="col-lg-6 col-md-8 login-box">
                <div class="col-lg-12 login-key"> <br/>
<!--                    <i class="fa fa-key" aria-hidden="true"></i>-->
                    <img src="<?php echo $logo; ?>" alt="<?php echo $pname; ?>" class="logo">
                </div>
                <div class="col-lg-12 login-title">
<!--                    ADMIN PANEL-->
                </div>

                <div class="col-lg-12 login-form">
                    <div class="col-lg-12 login-form">
                        <form>
                            <div class="form-group">
                                <label class="form-control-label">USERNAME</label>
                                <input type="text" class="form-control" id="user">
                            </div>
                            <div class="form-group">
                                <label class="form-control-label">PASSWORD</label>
                                <input type="password" class="form-control" id="pass">
                            </div>

                            <div class="col-lg-12 loginbttm">
                                <div class="col-lg-6 login-btm login-text">
                                    <!-- Error Message -->
                                </div>
                                <div class="col-lg-6 login-btm login-button">
<button id="loginbutton" type="button" class="btn btn-outline-primary">LOGIN</button>
<button type="reset" id="breset" class="btn btn-outline-primary">CANCEL</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    
                    
                    <p style="margin:0px 15px 20px 0; float:right;"> <a id="forgot" href="<?php echo site_url('login/forgot'); ?>"> [ Forgot Password ] </a> </p> <br> <div class="clear"></div>

                    
                </div>
                <div class="col-lg-12 col-md-12">
                                <p>&copy; Copyrights <b> <a id="brand" href="http://dswip.com" target="_blank"> <?php echo $pname.'&nbsp;'.date('Y'); ?> </a> </b> <br> All rights reserved.</p>
                </div>
            </div>
        </div>
<!-- partial -->
</div>
  
    </body>
</html>
