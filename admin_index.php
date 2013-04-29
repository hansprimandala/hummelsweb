<html>
	<head>
		<title>Administration Page</title>
	</head>
	<body>
		<form action = "hummel_controller.php" method = "post">
			<table>
				<tr>
					<td>Input your email address</td>
					<td><input type = "text" name = "adminEmail"></td>
				</tr>
				<tr>
					<td>Input your password</td>
					<td><input type = "password" name = "adminPass"></td>
				</tr>
				<tr>
					<td><input type = "hidden" name = "input" value = "loginAdmin"></td>
					<td><input type = "submit" value = "Log In"></td>
				</tr>
			</table>
		</form>
		<?php 
			if(!isset($_GET["message"])){
				
			}
			else if(isset($_GET["message"])){
				$message = $_GET["message"];
				
				if($message == "empty_fill"){
					echo "<font color = red>Your admin email or password is empty. You have to fill it first before log in</font>";
				}
				else if($message == "error_login"){
					echo "<font color = red>Your admin email or password might be wrong. Please input the correct one</font>";
				}
				else if($message == "cannot_jump_admin"){
					echo "<font color = red>You cannot jump to the admin page! You have to log in first before going to admin page</font>";
				}
				else if($message == "logged_out"){
					echo "<font color = red>You have logged out. Thank you for working today!</font>";
				}
			}
		?>
	</body>
</html>