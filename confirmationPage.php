<?php session_start();
include("hummel_model.php");
//$regArray = getAllRegency();
//$subArray = getAllSubDistrict();

	if(!isset($_SESSION["loginArray"]) || !isset($_SESSION["cartName"])){
		//do nothing
		echo "<font color = red>Please go back, there seems to be an error here</font>";
	}
	else if(isset($_SESSION["loginArray"]) && isset($_SESSION["cartName"]) && isset($_GET["firstIndex"]) && isset($_GET["lastIndex"])  && isset($_SESSION["allPrice"])){
		$shipCost = 0.0;
		$allPrice = $_SESSION["allPrice"];
		if(isset($_SESSION["address"]) && isset($_SESSION["postalCode"]) && isset($_SESSION["phoneNumber"]) && isset($_SESSION["regency"]) && 
		isset($_SESSION["subDistrict"]) && isset($_SESSION["delService"])){
			$address1 = $_SESSION["address"];
			$postalCode1 = $_SESSION["postalCode"];
			$phoneNumber1 = $_SESSION["phoneNumber"];
			$regency1 = $_SESSION["regency"];
			$subDistrict1 = $_SESSION["subDistrict"];
			$firstIndex = $_GET["firstIndex"];
			$lastIndex = $_GET["lastIndex"];
			$delService1 = $_SESSION["delService"];
			
			if($address1 == "" || $postalCode1 == "" || $phoneNumber1 == ""){
				header("Location:shipping.php?firstIndex=$firstIndex&lastIndex=$lastIndex&message=empty_field");
			}
			else if($address1 != "" && $postalCode1 != "" && $phoneNumber1 != ""){
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
				$shiPrice = 0.0;
				
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
				<H1>Your personal data</H1>
				<table border = "1">
					<tr>
						<td><B>Your user ID: </td>
						<td><?php echo $id;?></td>
					</tr>
					<tr>
						<td><B>Your first name</B></td>
						<td><?php echo $firstName;?></td>
					</tr>
					<tr>
						<td><B>Your last name</B></td>
						<td><?php echo $lastName;?></td>
					</tr>
					<tr>
						<td><B>Your e-mail address</B></td>
						<td><?php echo $email;?></td>
					</tr>
					<tr>
						<td><B>Your address</B></td>
						<td><?php echo $address;?></td>
					</tr>
					<tr>
						<td><B>Your postal code</B></td>
						<td><?php echo $postalCode;?></td>
					</tr>
					<tr>
						<td><B>Your phone_number</B></td>
						<td><?php echo $phoneNumber;?></td>
					</tr>
					<tr>
						<td><B>Regency</B></td>
						<td><?php echo $regency;?></td>
					</tr>
					<tr>
						<td><B>Sub-district</B></td>
						<td><?php echo $subDistrict;?></td>
					</tr>
				</table>
				<br><br>
				<H1>These are the items that you are going to buy</H1>
				<table border = "1">
					<th>Item name</th>
					<th>Total bought</th>
					<th>Total price</th>
					<th>Date added</th>
					<?php 		
					//retrieve the cart data
					$jsonCart = viewCart($_SESSION["cartName"]);
					$cartArray = json_decode($jsonCart,true);
					
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
				<H1>Ship the items to the address below</H1>
				<table border = "1">
					<tr>
						<td><B>Address is</B></td>
						<td><?php echo $address1; ?></td>
					</tr>
					<tr>
						<td><B>Postal code is</B></td>
						<td><?php echo $postalCode1; ?></td>
					</tr>
					<tr>
						<td><B>Phone number is</B></td>
						<td><?php echo $phoneNumber1; ?></td>
					</tr>
					<tr>
						<td><B>Regency is</B></td>
						<td><?php echo $regency1; ?></td>
					</tr>
					<tr>
						<td><B>Sub district is</B></td>
						<td><?php echo $subDistrict1; ?></td>
					</tr>
					<tr>
						<td><B>Delivery service is</B></td>
						<td><?php echo $delService1; ?></td>
					</tr>
					<tr>
						<td><B>Shipping Price is</B></td>
						<td><?php 
						$shipCost = getShipPrice($regency1,$subDistrict1,$delService1);
						echo $shipCost; ?></td>
					</tr>
				</table>
				<?php
			}
		}
		?>
		<br><br>
		Tranfer the money to:<br>
		<form action = "hummel_controller.php" method = "post">
			<input type = "radio" name = "bank" value = "Bank BCA">Bank BCA<br>
			<input type = "radio" name = "bank" value = "Bank Mandiri">Bank Mandiri<br>
			<input type = "hidden" name = "firstIndex" value = "<?php echo $firstIndex; ?>">
			<input type = "hidden" name = "lastIndex" value = "<?php echo $lastIndex; ?>">
			<input type = "hidden" name = "shipCost" value = "<?php echo $shipCost; ?>">
			<input type = "hidden" name = "allPrice" value = "<?php echo $allPrice; ?>">
			<input type = "hidden" name = "input" value = "confirmTransaction">
			<input type = "submit" value = "Confirm Transaction">
		</form>
		<hr>
			<form action = "shipping.php" method = "get">
				<input type = "hidden" name = "allPrice" value = "<?php echo $allPrice; ?>">
				<input type = "hidden" name = "firstIndex" value = "<?php echo $firstIndex; ?>">
				<input type = "hidden" name = "lastIndex" value = "<?php echo $lastIndex; ?>">
				<input type = "submit" value = "Back to Shipping Page">
			</form>
		<hr>
		<form action = "index.php" method = "get">
			<input type = "hidden" name = "firstIndex" value = "<?php echo $firstIndex; ?>">
			<input type = "hidden" name = "lastIndex" value = "<?php echo $lastIndex; ?>">
			<input type = "submit" value = "Back to Product Page">
		</form>
		<?php
	}
?>