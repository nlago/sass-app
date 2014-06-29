<?php
ob_start();
// TODO: Add option-functionality to resend email if password forgot
// TODO: sql make 'img' of database to NOT NULL & refactor name to 'img_location'
require 'inc/init.php';

// if there is an active log in process redirect to index.php; load page only if no
// logged in user exists
$general->logged_in_protect();
$page_title = "Log In";

?>

<!-- ************  IF THE LOG IN BUTTON IS SUBMITTED ************** -->
<!-- 1. It's preferred to create a hidden input for security purposes, as well verifying that this input or another dummy
is not filled, as to protect from robots/spam.
	2. It's preferred to have the php code on the beginning of the script and the view code afterwards. More neat & organized.-->
<?php /**
 * @return bool
 */
function isLoginBtnPressed() {
	return isset($_POST['hidden_submit_pressed']) && empty($_POST['hidden_submit_pressed']);
}

/**
 * @return bool
 */
function isForgotBtnPressed() {
	return isset($_POST['hidden_forgot_pressed']) && empty($_POST['hidden_forgot_pressed']);
}

if (isLoginBtnPressed()) {

   $email = trim($_POST['login_email']);
   $password = trim($_POST['login_password']);

   try {
      // check if credentials are correct. If they are not, an exception occurs.
      $users->login($email, $password);
      // destroying the old session id
      //and creating a new one. protect from session fixation attack.
      session_regenerate_id(true);
      // The user's id is now set into the user's session  in the form of $_SESSION['id']
      $_SESSION['email'] = $email;

      // if there is an active log in process redirect to index.php; load page only if no logged in user exists
      $general->logged_in_protect();
   } catch (Exception $e) {
      $errors[] = $e->getMessage();
   }
} else if(isForgotBtnPressed()) {

}
?>
<!DOCTYPE html>
<!--[if lt IE 7]>
<html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>
<html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>
<html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js"> <!--<![endif]-->
<head>

   <title>Login - Canvas Admin</title>
   <meta charset="utf-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
   <meta name="description" content="">
   <meta name="author" content=""/>
   <link rel="shortcut icon" href="<?php echo BASE_URL; ?>img/logos/logo-login.png">

   <link rel="stylesheet"
         href="http://fonts.googleapis.com/css?family=Open+Sans:400italic,600italic,800italic,400,600,800"
         type="text/css">
   <link rel="stylesheet" href="<?php echo BASE_URL; ?>css/font-awesome.min.css" type="text/css"/>
   <link rel="stylesheet" href="<?php echo BASE_URL; ?>css/bootstrap.min.css" type="text/css"/>
   <link rel="stylesheet" href="<?php echo BASE_URL; ?>js/libs/css/ui-lightness/jquery-ui-1.9.2.custom.css"
         type="text/css"/>

   <link rel="stylesheet" href="<?php echo BASE_URL; ?>css/App.css" type="text/css"/>
   <link rel="stylesheet" href="<?php echo BASE_URL; ?>css/Login.css" type="text/css"/>

   <link rel="stylesheet" href="<?php echo BASE_URL; ?>css/custom.css" type="text/css"/>

</head>

<body>

<?php if(isForgotBtnPressed()) { ?>
	<div id="login-container">

		<div id="logo">
			<a href="<?php echo BASE_URL; ?>login.php">
				<img src="<?php echo BASE_URL; ?>img/logos/logo-login.png" alt="Logo"/>
			</a>
		</div>

		<!-- /#forgot -->
		<div id="login">
			<h4>Please enter your acg email.</h4>
			<form method="post" id="login-form" action="login.php" class="form">
				<div class="form-group">
					<label for="login-email">Email</label>
					<input type="email" class="form-control" id="login-email" name="login_email" placeholder="Email">
				</div>

				<div class="form-group">
					<input type="hidden" name="hidden_submit_pressed">
					<button type="submit" id="login-btn" name="login" class="btn btn-primary btn-block">Continue <i
							class="glyphicon glyphicon-envelope"></i></button>
				</div>
			</form>

			<?php
			if (empty($errors) === false) {
				?>
				<div class="alert alert-danger">
					<a class="close" data-dismiss="alert" href="#" aria-hidden="true">×</a>
					<strong>Oh snap!</strong><?php echo '<p>' . implode('</p><p>', $errors) . '</p>'; ?>
				</div>
			<?php
			}
			?>
		</div>
		<!-- /#forgot -->

	</div>
	<!-- /#forgot-container -->

<?php } else { ?>
<div id="login-container">

   <div id="logo">
      <a href="<?php echo BASE_URL; ?>login.php">
         <img src="<?php echo BASE_URL; ?>img/logos/logo-login.png" alt="Logo"/>
      </a>
   </div>

	<!-- /#login -->
	<div id="login">
      <h4>Welcome to SASS-Management System</h4>
      <h5>Please sign in to get access.</h5>
      <form method="post" id="login-form" action="login.php" class="form">
         <div class="form-group">
            <label for="login-email">Username</label>
            <input type="email" class="form-control" id="login-email" name="login_email" placeholder="Email">
         </div>

         <div class="form-group">
            <label for="login-password">Password</label>
            <input type="password" class="form-control" id="login-password" name="login_password"
                   placeholder="Password">
         </div>

         <div class="form-group">
            <input type="hidden" name="hidden_submit_pressed">
            <button type="submit" id="login-btn" name="login" class="btn btn-primary btn-block">Signin &nbsp; <i
                  class="fa fa-play-circle"></i></button>
         </div>
      </form>

	   <form method="post" id="forgot-form" action="login.php" class="form">
		   <div class="form-group text-center">
			   <input type="hidden" name="hidden_forgot_pressed">
			   <button type="submit" name="forgot" class="btn btn-default">
				   Forgot Password?
			   </button>
		   </div>
		</form>
	   <?php
         if (empty($errors) === false) {
            ?>
            <div class="alert alert-danger">
               <a class="close" data-dismiss="alert" href="#" aria-hidden="true">×</a>
               <strong>Oh snap!</strong><?php echo '<p>' . implode('</p><p>', $errors) . '</p>'; ?>
            </div>
         <?php
         }
         ?>
   </div>
   <!-- /#login -->

</div>
<!-- /#login-container -->

<?php } ?>
<script src="<?php echo BASE_URL; ?>js/libs/jquery-1.9.1.min.js"></script>
<script src="<?php echo BASE_URL; ?>js/libs/jquery-ui-1.9.2.custom.min.js"></script>
<script src="<?php echo BASE_URL; ?>js/libs/bootstrap.min.js"></script>

<script src="<?php echo BASE_URL; ?>js/App.js"></script>

<script src=".<?php echo BASE_URL; ?>s/Login.js"></script>
</body>
</html>