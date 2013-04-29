<?php session_start();
include("hummel_model.php");
	if(!isset($_SESSION["loginArray"])){
		echo "<font color = red>Sorry, this page cannot be accessed. You have to log in first</font>";
	}
	else if(isset($_SESSION["loginArray"])){
		?>
		<H1>Input your confirmation code for knowing the detail of your items in confirmed cart</H1>
		<form action = "hummel_controller_2.php" method = "post">
			<table>
				<tr>
					<td>Input your confirmation code</td>
					<td><input type = "text" name = "confirmCode"></td>
				</tr>
				<tr>
					<td><input type = "hidden" name = "input" value = "checkConfirm"></td>
					<td><input type = "submit" value = "Check Confirmation Status"></td>
				</tr>
			</table>
		</form>
		<?php
		
		if(isset($_SESSION["status"]) && isset($_SESSION["itemConfirmArray"]) && isset($_SESSION["addressArray"]) && isset($_GET["confirmCode"])){
			echo "Your confirmation item for ".$_GET["confirmCode"]." : <B><I>".$_SESSION["status"]."</I></B><br>";
			
			?>
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
				</table>		
				<br><br>
				<H1>The address where the items are shipped</H1>
				<?php for($i = 0; $i < sizeof($_SESSION["addressArray"]) - 1; $i++){
					$address = $_SESSION["addressArray"][$i];
				?>
				<table border = "1">
					<tr>
						<td><B>Address is</B></td>
						<td><?php echo $address["address"]; ?></td>
					</tr>
					<tr>
						<td><B>Postal code is</B></td>
						<td><?php echo $address["postal_code"]; ?></td>
					</tr>
					<tr>
						<td><B>Phone number is</B></td>
						<td><?php echo $address["phone_number"]; ?></td>
					</tr>
					<tr>
						<td><B>Regency is</B></td>
						<td><?php echo $address["regency"]; ?></td>
					</tr>
					<tr>
						<td><B>Sub district is</B></td>
						<td><?php echo $address["sub_district"]; ?></td>
					</tr>
					<!-- <tr>
						<td><B>Delivery service is</B></td>
						<td><?php echo $delService1; ?></td>
					</tr>
					<tr>
						<td><B>Shipping Price is</B></td>
						<td><?php 
						$shipCost = getShipPrice($regency1,$subDistrict1,$delService1);
						echo $shipCost; ?></td>
					</tr> -->
				</table>
				<br><br>
				<?php } ?>
			<?php
		}
		
		?>
		<H1>Send Message To Admin</H1>
		<form action = "hummel_controller_2.php" method = "post">
			<table>
				<tr>
					<td>Input your confirmation code</td>
					<td><input type = "text" name = "confirmCodeMessage"></td>
				</tr>
				<tr>
					<td>Type in your message indicating your accomplished payment</td>
					<td>
						<!-- <input type = "text" name = "confirmMessages" value ="aaaa"> -->
						<textarea name = "confirmMessages" rows = "10" cols = "100">
							Dear administrator, i hereby want to state that the confirmation with the code as typed above, has been paid. Please check it for more clarification
						</textarea> 
					</td>
				</tr>
				<tr>
					<td><input type = "hidden" name = "input" value = "sendConfirmMessage"></td>
					<td><input type = "submit" value = "Send Message"></td>
				</tr>
			</table>
		</form>
		<?php
		
		if(!isset($_GET["message"])){
			//do nothing
		}
		else if(isset($_GET["message"])){
			$message = $_GET["message"];
			
			if($message == "empty_field"){
				echo "<font color = red>The field(s) must not be empty</font>";
			}
			else if($message == "message_sent"){
				echo "<font color = red>Your message has been sent</font>";
			}
		}
		?>
		<hr>
		<form action = "index.php">
			<input type = "submit" value = "Back">
		</form>
		<?php
	}
?>