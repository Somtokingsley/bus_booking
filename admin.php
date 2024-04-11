
<!DOCTYPE html>
<html>
	<head>
		<?php include('header.php') ?>
        <?php 
        // session_start();
        // if(isset($_SESSION['login_id'])){
        //     header('Location:home.php');
        // }
        ?>
		<title>Admin Login | Bus Booking</title>
	</head>
    <style>
        body {
    background-image: url(./assets/img/bus.jpg);
    height: 96vh;
    background-position: center;
    background-repeat: no-repeat;
    background-size: cover;
}
    </style>
	<body id='login-body' class="bg-light">
        <div class="card col-md-4 offset-md-4 mt-4" style="padding-top: 25px;">
                <div class="card-header-edge text-sky-blue" style="padding-left: 20px;">
                    <strong style="font-size:25px;">Login</strong>
                </div>
            <div class="card-body">
                     <form id="login-frm">
                        <div class="form-group">
                            <label>Username</label>
                            <input type="username" name="username" class="form-control">
                        </div>
                        <!-- <div class="form-group">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control">
                        </div>  -->
                        <div class="form-group mb-2">
                        <label for="password" class="control-label">Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="password" name="password" required value="<?php echo isset($meta['password']) ? $meta['password'] : '' ?>">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                        <i class="fas fa-eye" id="toggleIcon"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group text-right">
                            <button class="btn btn-info btn-block" name="submit">Login</button>
                        </div>
                        
                    </form>
            </div>
        </div>

		</body>

        <script>
            $(document).ready(function(){
                $('#login-frm').submit(function(e){
                    e.preventDefault()
                    console.log($(this).serialize())
                    $('#login-frm button').attr('disable',true)
                    $('#login-frm button').html('Please wait...')

                    $.ajax({
                        url:'./login_auth.php',
                        method:'POST',
                        data:$(this).serialize(),
                        error:err=>{
                            console.log(err)
                            alert('An error occured');
                            $('#login-frm button').removeAttr('disable')
                            $('#login-frm button').html('Login')
                        },
                        success:function(resp){
                            if(resp == 1){
                                location.replace('index.php?page=home')
                            }else{
                                alert("Incorrect username or password.")
                                $('#login-frm button').removeAttr('disable')
                                $('#login-frm button').html('Login')
                            }
                        }
                    })

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
</html>