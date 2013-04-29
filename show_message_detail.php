<?php session_start();
include("hummel_model.php");
	if(!isset($_SESSION["jsonLogin"])){
		header("Location:admin_index.php?message=cannot_jump_admin");
	}
	else if(isset($_SESSION["jsonLogin"])){
		$email = "";
		$firstMessage = 0;
		$totalView = 0;
		$confirmCode = "";
		if(isset($_GET["id"]) && isset($_SESSION["messageDetailArray"]) && isset($_GET["firstMessage"]) && isset($_GET["totalView"])){	
			$firstMessage = $_GET["firstMessage"];
			$totalView = $_GET["totalView"];
			$id = $_GET["id"];
			$idMessage = $id;
			
			for($i = 0; $i < sizeof($_SESSION["messageDetailArray"]) - 1; $i++){
				$message = $_SESSION["messageDetailArray"][$i];
				$confirmCode = $message["confirmation_code"];
				echo "Confirmation Code is <b>".$confirmCode."</b><br><br>";
				echo "The message is <b>".$message["message"]."</b><br>";
			}
		}
		
		?>
		<h1>Check the transaction according to the confirmation code</h1>
		<form action = "hummel_controller_2.php" method = "post">
			<input type = "hidden" name = "id" value = "<?php echo $id; ?>">
			<input type = "hidden" name = "firstMessage" value = "<?php echo $firstMessage; ?>">
			<input type = "hidden" name = "totalView" value = "<?php echo $totalView; ?>">
			<input type = "hidden" name = "confirmCode" value = "<?php echo $confirmCode; ?>">
			<input type = "hidden" name = "input" value = "checkTransactionStatus">
			<input type = "submit" value = "Check Transaction">
		</form>
		
		<?php 
			if(isset($_SESSION["itemConfirmArray"]) && isset($_SESSION["userArray"])){
				$userArray = $_SESSION["userArray"];
				$id = 0;
				$firstName = "";
				$lastName = "";
				//$email = "";
				$address = "";
				$postalCode = "";
				$phoneNumber = "";
				$regency = "";
				$subDistrict = "";
				
				for($i = 0; $i < sizeof($userArray)-1; $i++){
					$user = $userArray[$i];
					$id = $user["iduser"];
					$firstName = $user["first_name"];
					$lastName = $user["last_name"];
					$email = $user["email"];
					$address = $user["address"];
					$postalCode = $user["postal_code"];
					$phoneNumber = $user["phone_number"];
					$regency = $user["regency"];
					$subDistrict = $user["sub_district"];
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
				</table>
				
				<H1>These are the items that you have bought (according to the confirmation code)</H1>
				<table border = "1">
					<th>Item name</th>
					<th>Total bought</th>
					<th>Total price</th>
					<th>Date added</th>
					<?php 		
					//retrieve the cart data
					//$jsonCart = viewCart($_SESSION["cartName"]);
					$cartArray = $_SESSION["itemConfirmArray"];
					
					if($cartArray[0] == false){
						echo "<font color = red>No items inside the shopping cart</font>";
					}
					else{
						//$idItem = 0; //not necessary i guess
						//$totalBought = 0; //not necessary i guess
						for($i = 0; $i < sizeof($cartArray)-1; $i++){
							$cart = $cartArray[$i];
							$idItem = $cart["id_item"];
							//$totalBought = $cart["total_bought"]; //not necessary i guess
							$itemName = getItemName($idItem);
							echo "<tr>";
							echo "<td>".$itemName."</td>";
							echo "<td>".$cart["total_bought"]."</td>";
							echo "<td>".$cart["total_price"]."</td>";
							echo "<td>".$cart["date"]."</td>";
							echo "</tr>";
						}
					}
					?>
				</table><br><br>
				<?php 
			}
		
		
		?>
		<h1>Confirmation Section</h1>
		<table border = "1">
			<th>Email for accomplished payment</th>
			<th>&nbsp;</th>
			<th>Email for unaccomplished payment</th>
			<tr>
				<td>
					<form action = "hummel_controller_2.php" method = "post">
						<input type = "hidden" name = "email" value = "<?php echo $email; ?>">
						<input type = "hidden" name = "id" value = "<?php echo $idMessage; ?>">
						<input type = "hidden" name = "firstMessage" value = "<?php echo $firstMessage; ?>">
						<input type = "hidden" name = "totalView" value = "<?php echo $totalView; ?>">
						<input type = "hidden" name = "confirmCode" value = "<?php echo $confirmCode; ?>">
						<input type = "hidden" name = "input" value = "sendAccomplished">
						<input type = "submit" value = "Send The E-Mail">
					</form>
				</td>
				<td>&nbsp;</td>
				<td>
					<form action = "hummel_controller_2.php" method = "post">
						<input type = "hidden" name = "email" value = "<?php echo $email; ?>">
						<input type = "hidden" name = "id" value = "<?php echo $idMessage; ?>">
						<input type = "hidden" name = "firstMessage" value = "<?php echo $firstMessage; ?>">
						<input type = "hidden" name = "totalView" value = "<?php echo $totalView; ?>">
						<input type = "hidden" name = "confirmCode" value = "<?php echo $confirmCode; ?>">
						<input type = "hidden" name = "input" value = "sendUnaccomplished">
						<input type = "submit" value = "Send The E-Mail">
					</form>
				</td>
			</tr>
		</table>
		
		<h1>Add Shipping Code</h1> <!-- will also send e-maila nd update status shipping and status payment, aside from storing the items into the tabled atabase-->
		<form action = "hummel_controller_2.php" method = "post">
			<table border = "1">
				<tr>
					<td>Confirmation Code </td>
					<td><?php echo "<b>".$confirmCode."</b>"; ?></td>
				</tr>
				<tr>
					<td>Shipping Code</td>
					<td><input type = "text" name = "shippingCode"></td>
				</tr>
				<tr>
					<td>
						<input type = "hidden" name = "id" value = "<?php echo $idMessage; ?>">
						<input type = "hidden" name = "firstMessage" value = "<?php echo $firstMessage; ?>">
						<input type = "hidden" name = "totalView" value = "<?php echo $totalView; ?>">
						<input type = "hidden" name = "confirmCode" value = "<?php echo $confirmCode; ?>">
						<input type = "hidden" name = "input" value = "sendShipping">
					</td>
					<td>
						<input type = "submit" value = "Submit The Shipping Code">
					</td>
				</tr>			
			</table>
		</form>
		
		<?php 
			if(isset($_SESSION["lastMessage"])){
				$lastMessage = $_SESSION["lastMessage"];
				echo $lastMessage;
				unset($_SESSION["lastMessage"]);
			}
			else{
				echo "";
			}
			
			if(!isset($_GET["message"])){}
			else if(isset($_GET["message"])){
				$message = $_GET["message"];
				
				if($message == "email_sent"){
					echo "<font color = red>E-mail has been sent</font>";
				}
				else if($message == "ship_done"){
					echo "<font color = red>Shipping code has been submitted previously</font>";
				}
				else if($message == "ship_succeess"){
					echo "<font color = red>Shipping code has been submitted succesfully</font>";
				}
				else if($message == "empty_field"){
					echo "<font color = red>The field must bot be empty!</font>";
				}
			}
		?>
		<br><br><hr>
		<form action = "hummel_controller_2.php" method = "post">
			<input type = "hidden" name = "firstMessage" value = "<?php echo $firstMessage; ?>">
			<input type = "hidden" name = "totalView" value = "<?php echo $totalView; ?>">
			<input type = "hidden" name = "input" value = "viewMessage">
			<input type = "submit" value = "Back">
		</form>
		<?php
	}
?>