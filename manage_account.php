<?php
session_start();
include('db_connect.php');
	$qry = $conn->query("SELECT * FROM users where id = ".$_SESSION['login_id'])->fetch_array();
	foreach($qry as $k => $val){
		$meta[$k] =  $val;
	}
?>
<div class="container-fluid">
	<form id="manage_user">
		<div class="col-md-12">
			<div class="form-group mb-2">
				<label for="name" class="control-label">Name</label>
				<input type="hidden" class="form-control" id="id" name="id" value='<?php echo isset($meta['id']) ? $meta['id'] : '' ?>' required="">
				<input type="text" class="form-control" id="name" name="name" required="" value="<?php echo isset($meta['name']) ? $meta['name'] : '' ?>">
			</div>
			<div class="form-group mb-2">
				<label for="username" class="control-label">User Name</label>
				<input type="text" class="form-control" id="username" name="username" required value="<?php echo isset($meta['username']) ? $meta['username'] : '' ?>">
			</div>
			
			<div class="form-group mb-2">
			<label for="password" class="control-label">Re-enter Password</label>
				<div class="input-group">
					<input type="password" class="form-control" id="password" name="password" required value="<?php echo isset($meta['password']) ? $meta['password'] : '' ?>">
					<div class="input-group-append">
						<button class="btn btn-outline-secondary" type="button" id="togglePassword">
							<i class="fas fa-eye" id="toggleIcon"></i>
						</button>
					</div>
				</div>
			</div>
		
		</div>
	</form>
</div>
<script>
	$('#manage_user').submit(function(e){
		e.preventDefault()
		start_load()
		$.ajax({
			url:'./update_account.php',
			method:'POST',
			data:$(this).serialize(),
			error:err=>{
				console.log(err)
    			end_load()
    			alert_toast('An error occured','danger');
			},
			success:function(resp){
				if(resp == 1){
    				$('.modal').modal('hide')
    				alert_toast('Account successfully updated','success');
    				setTimeout(function(){
    					location.reload()
    				},500)
				}
			}
		})
	})

    $(document).ready(function(){
        $('#togglePassword').click(function(){
            var passwordField = $('#password');
            var passwordFieldType = passwordField.attr('type');
            if(passwordFieldType == 'password') {
                passwordField.attr('type', 'text');
                $('#toggleIcon').removeClass('fa-eye').addClass('fa-eye-slash');
            } else {
                passwordField.attr('type', 'password');
                $('#toggleIcon').removeClass('fa-eye-slash').addClass('fa-eye');
            }
        });
    });

</script>