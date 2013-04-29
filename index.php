<?php session_start();
include("hummel_model.php");

$regency = "";
if(!isset($_GET["regency"])){

}
else if(isset($_GET["regency"])){
	$regency = $_GET["regency"];
	//echo $regency;
}
$regArray = getAllRegency();
$subArray = getAllSubDistrict($regency);
	//Can show product without even login
	
if(!isset($_GET["operation"])){
	//do nothing
}
else if(isset($_GET["operation"])){
	$operation = $_GET["operation"];
	
	if($operation == "destroyCart"){
		unset($_SESSION["cartName"]); //it is because the shopping has been done
	}
}
?>
<html>
	<head><title>Welcome to www.hummels.com</title>
	<script type = "text/javascript">
		function takesValue(email){
			//document.write(email);
			//window.location.href = "index.php?emailRegis="+email;
			var emails = email;
		}
	</script>
	</head>
	<body>
		<?php if(!isset($_SESSION["loginArray"])){ ?>
		<form action = "hummel_controller.php" method = "post">
			<h1>Login</h1>
			<table>
				<tr>
					<td>E-mail address</td>
					<td><input type = "text" name = "emailLogin"></td>
				</tr>
				<tr>
					<td>Password</td>
					<td><input type = "password" name = "passLogin"></td>
				</tr>
				<tr>
					<td><input type = "hidden" name = "input" value = "loginUser"></td>
					<td><input type = "submit" value = "Log In"></td>
				</tr>
			</table>
		</form>
		
		<form action = "hummel_controller.php" method = "post" onsubmit = "takesValue(this.email.value)">
			<h1>Registration</h1>
			<?php 
				//$emailRegis = "";
				if(isset($_GET["emailRegis"])){
					$emailRegis = $_GET["emailRegis"];
					echo $emailRegis;
				}
				else{
					$emailRegis = "";
				}
			?>
			<table>
				<tr>
					<td><b>Please specify first the regency which do you live in</b></td>
					<td>
						<select name = "regency" id = "regSelect">
						<?php 
						foreach($regArray as $reg){
							echo "<option>".$reg."</option>";
						}
						?>
						</select>
					</td>
				</tr>
				<script type = "text/javascript">
					var regChosen = '<?php echo $regency; ?>';
					if(regChosen == ''){
					}
					else if(regChosen != ''){
						var regSel = document.getElementById("regSelect");
						regSel.value = regChosen;
					}
				</script>
				<script type = "text/javascript">
					var regSel = document.getElementById("regSelect").onchange = function(){
					var regency = this.options[this.selectedIndex].text;
					window.location.href = "index.php?regency="+regency;//+"&firstName="+firstName;
					//this.value = strUser;
					//document.write(strUser); //VALUE CAN BE TAKEN ACTUALLY
					};
				</script>
				<tr>
					<td><b>Now please choose the sub district of your address</b></td>
					<td>
						<select name = "subDistrict">
						<?php
						foreach($subArray as $sub){
							echo "<option>".$sub."</option>";
						}
						?>
						</select>
					</td>
				</tr>
				<tr>
					<td colspan = "2"><hr></td>
				</tr>
				<tr>
					<td>Input your first name</td>
					<td><input type = "text" name = "firstName" id = "fN"></td>
				</tr>
				<tr>
					<td>Input your last name</td>
					<td><input type = "text" name = "lastName"></td>
				</tr>
				<tr>
					<td>Input your e-mail address</td>
					<td><input type = "text" name = "email" value = ""></td>
				</tr>
				<tr>
					<td>Input your password</td>
					<td><input type = "password" name = "pass"></td>
				</tr>
				<tr>
					<td>Re-type your password</td>
					<td><input type = "password" name = "rePass"></td>
				</tr>
				<tr>
					<td>Input your address</td>
					<td><input type = "text" name = "address"></td>
				</tr>
				<tr>
					<td>Input your postal code</td>
					<td><input type = "text" name = "postalCode"></td>
				</tr>
				<tr>
					<td>Input your phone number</td>
					<td><input type = "text" name = "phone"></td>
				</tr>
				<tr>
					<td><input type = "hidden" name = "input" value = "regisUser"></td>
					<td><input type = "submit" value = "Register"></td>
				</tr>
			</table>
		</form>
		<?php }
			else if(isset($_SESSION["loginArray"])){
				$loginArray = $_SESSION["loginArray"];
				$id = 0;
				$firstName = "";
				$lastName = "";
				$email = "";
				$address = "";
				$postalCode = "";
				$phoneNumber = "";
				$regency = "";
				$subDistrict = "";
				
				for($i = 0; $i < sizeof($loginArray)-1; $i++){
					$login = $loginArray[$i];
					$id = $login["iduser"];
					$firstName = $login["first_name"];
					$lastName = $login["last_name"];
					$email = $login["email"];
					$address = $login["address"];
					$postalCode = $login["postal_code"];
					$phoneNumber = $login["phone_number"];
					$regency = $login["regency"];
					$subDistrict = $login["sub_district"];
				}
				?>
				<table border = "1">
					<tr>
						<td>Your user ID: </td>
						<td><?php echo $id;?></td>
					</tr>
					<tr>
						<td>Your first name: </td>
						<td><?php echo $firstName;?></td>
					</tr>
					<tr>
						<td>Your last name: </td>
						<td><?php echo $lastName;?></td>
					</tr>
					<tr>
						<td>Your e-mail address: </td>
						<td><?php echo $email;?></td>
					</tr>
					<tr>
						<td>Your address: </td>
						<td><?php echo $address;?></td>
					</tr>
					<tr>
						<td>Your postal code: </td>
						<td><?php echo $postalCode;?></td>
					</tr>
					<tr>
						<td>Your phone_number: </td>
						<td><?php echo $phoneNumber;?></td>
					</tr>
					<tr>
						<td>Regency: </td>
						<td><?php echo $regency;?></td>
					</tr>
					<tr>
						<td>Sub-district: </td>
						<td><?php echo $subDistrict;?></td>
					</tr>
				</table><br><br>
				
				<h1>JAVASCRIPT TAKES VALUE ULANG</h1>
				<form action = "hummel_controller_2.php" method = "post">
					<input type = "hidden" name = "idUser" value = "<?php echo $id; ?>">
					<input type = "hidden" name = "input" value = "checkAvailableCart">
					<input type = "submit" value = "Check Your Shopping Cart">
				</form>
				<form action = "checkConfirmation.php" method = "post">
					<input type = "submit" value = "Check your item confirmation">
				</form>
				<form action = "hummel_controller.php" method = "post">
					<input type = "hidden" name = "input" value = "logoutUser">
					<input type = "submit" value = "Log Out">
				</form>
				<?php
			}
			?>
		<?php 
			if(!isset($_GET["message"])){
				//do nothing
			}
			else if(isset($_GET["message"])){
				$message = $_GET["message"];
				
				if($message == "empty_field"){
					echo "<font color = red>All fields must be filled in order to proceed to registration!</font>";
				}
				else if($message == "email_exist"){
					echo "<font color = red>This e-mail address has ever been recorded in our system. Please find another one</font>";
				}
				else if($message == "pass_not_same"){
					echo "<font color = red>The password and the the password validation does not match. Please enter the correct one</font>";
				}
				else if($message == "register_success"){
					echo "<font color = red>Registration Successful! Please Log In to start shopping!</font>";
				}
				else if($message == "login_empty"){
					echo "<font color = red>All fields must be filled in order to proceed to log-in</font>";
				}
				else if($message == "login_fail"){
					echo "<font color = red>Your e-mail address or password might be wrong</font>";
				}
				else if($message == "email_invalid"){
					echo "<font color = red>Your e-mail address is invalid</font>";
				}
			}
		?>
		<br><br>
		<?php
			//echo "<a href = hummel_controller.php?input=viewProduct&firstIndex=$firstIndex&lastIndex=5>Show Available Products</a>";

					/*if(!isset($_SESSION["jsonProduct"])){
						echo "No products to see!";
					}
					else if(isset($_SESSION["jsonProduct"])){
						$jsonProduct = $_SESSION["jsonProduct"];
						$productArray = json_decode($jsonProduct,true);
						
						if($productArray[0] == false){
							echo "No more products to see";
						}
						else{
							if(isset($_GET["firstIndex"]) && isset($_GET["lastIndex"])){
							$firstIndex = $_GET["firstIndex"];
							$lastIndex = $_GET["lastIndex"];
							viewProductIndex($firstIndex,$lastIndex,$productArray);
							}
							else{
								//a
								//$firstIndex = $_GET["first"];
								$firstIndex = getProductRows()-5; //test it again!
								$_SESSION["jsonProduct"] = viewProduct($firstIndex,5);
							$lastIndex = 5;
							viewProductIndex($firstIndex,$lastIndex,$productArray);
							}
						}
					} */	
					if(isset($_GET["firstIndex"]) && isset($_GET["lastIndex"])){
						$firstIndex = $_GET["firstIndex"];
						$lastIndex = $_GET["lastIndex"];
						
						$productArray = json_decode(viewProduct($firstIndex,$lastIndex),true); //return json product
						
						if($productArray[0] == false){
							echo "No products to see";
						}
						else{
							viewProductIndex($firstIndex,$lastIndex,$productArray);
						}
					}
					else{
						//first open index.php
						$firstIndex = getProductRows()-5; //test it again!
						$productArray = json_decode(viewProduct($firstIndex,5),true); //return json product
						
						if($productArray[0] == false){
							echo "No products to see";
						}
						else{
							viewProductIndex($firstIndex,5,$productArray);
						}
					}
		?>
		<?php 
			function viewProductIndex($firstIndex,$lastIndex,$productArray){
				?>
							<table>
								<?php 
								for($i = sizeof($productArray)-2; $i >=0; $i--){
									$product = $productArray[$i]; 
									$id = $product["idproduct"];
									$title = $product["product_photo_title"]; //correct already
									echo "<tr>";
									echo "<td><a href = hummel_controller.php?input=getProdDetailUser&id=$id&first=$firstIndex&last=$lastIndex>".$product["product_name"]."</a></td>"; //must be in A href for showing more details, UPDATE AND DELETE!
									echo "<td><img src = Product_Photo/$title width = 100 height = 100></td>";
									echo "</tr>";
								}
								?>
							</table>
							<?php
							
							?>
							<form action = "index.php" method = "get">
								<table>
									<tr>
										<td>
											<input type = "hidden" name = "firstIndex" value = "<?php echo $firstIndex + 5;?>"> <!-- Simply echo it! To test, use input type text first -->
											<input type = "hidden" name = "lastIndex" value = "<?php echo $lastIndex;?>">
											<!-- <input type = "hidden" name = "input" value = "viewProduct"> -->
											<?php if($firstIndex == getProductRows()-5){ ?>
											<input type = "submit" value = "Previous" disabled> <?php 
											}
											else{
											?>
											<input type = "submit" value = "Previous">	
											<?php
											}
											?>
										</td>
									</tr>
								</table>
							</form>
							<form action = "index.php" method = "get">
								<table>
									<tr>
										<td>
											<input type = "hidden" name = "firstIndex" value = "<?php echo $firstIndex - 5;?>"> <!-- Simply echo it! To test, use input type text first -->
											<input type = "hidden" name = "lastIndex" value = "<?php echo $lastIndex;?>">
											<!-- <input type = "hidden" name = "input" value = "viewProduct"> -->
											<?php if ($firstIndex <= 0){?>
											<input type = "submit" value = "Next" disabled> 
											<?php }
											else{
											?>
											<input type = "submit" value = "Next"> 
											<?php
											}
											?>
										</td>
									</tr>
								</table>
							</form>
							<br>
							<form action = "index.php" method = "get">
								<input type = "submit" value = "Refresh Page">
							</form>
							<?php
			}
		?>
	</body>
</html>
