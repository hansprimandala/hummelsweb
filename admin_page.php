<?php session_start();
include("hummel_model.php");
	if(!isset($_SESSION["jsonLogin"])){
		header("Location:admin_index.php?message=cannot_jump_admin");
	}
	else if(isset($_SESSION["jsonLogin"])){
		//echo "SUCCESS LOGIN";
		$jsonLogin = $_SESSION["jsonLogin"];
		
		$loginArray = json_decode($jsonLogin,true);
		
		if($loginArray[0] == false){
			echo "<font color = red>Something's wrong in retrieving your own data</font>";
		}
		else{
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
				$id = $login["idadmin"];
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
					<td>Your admin ID: </td>
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
			<?php
		}
		?><br><br>
		<hr>
		<form action = "hummel_controller.php" method = "post">
			Upload .xls file here to retrieve data of shipping cost<br>
			<input type = "file" name = "shippingFile"><br>
			<input type = "hidden" name = "input" value = "uploadShip">
			<input type = "submit" value = "Upload Shipping Cost Data">
		</form><br>
		
		<form action = "hummel_controller.php" method = "post">
			Press the button below to remove the data of the shipping cost<br>
			<input type = "hidden" name = "input" value = "removeShip">
			<input type = "submit" value = "Remove Shipping Cost Data">
		</form><br>
		
		<form action = "hummel_controller.php" method = "post">
			Press the button below to list down the shipping area data!<br>
			<input type = "hidden" name = "index" value = "0">
			<input type = "hidden" name = "input" value = "viewShip">
			<input type = "submit" value = "View Shipping Area Data">
		</form>
		<?php
		
		if(!isset($_GET["message"])){
			//do nothing
		}
		else if(isset($_GET["message"])){
			$message = $_GET["message"];
			
			if($message == "data_uploaded"){
				echo "<font color = red>Shipping file has been uploaded to the database</font>";
			}
			else if($message == "data_not_uploaded"){
				echo "<font color = red>You have not yet uploaded any file!</font>";
			}
			else if($message == "data_removed"){
				echo "<font color = red>Data has been removed</font>";
			}
			else if($message == "show_reg_dis"){
				if(isset($_SESSION["jsonRegency"]) && isset($_SESSION["jsonSubDis"]) && isset($_GET["index"])){
					
					//===== Sub-District Managament =====
					$subDisArray = json_decode($_SESSION["jsonSubDis"],true);
					echo "<h1>Sub District List</h1>";
					
					if($subDisArray[0] == false){
						echo "No Sub District Data has ever been recorded";
					}
					else{
					?>
					<table border = "1">
						<th>Regency</th>
						<th>Sub-District</th>
						<th>Reguler package price</th>
						<th>OK package price</th>
						<?php for($i = 0; $i < sizeof($subDisArray)-1; $i++){
							$subDis = $subDisArray[$i];
						?>
						<tr>
							<?php //get the regency name here 
								$regName = getRegencyName((int)$subDis["idshipping_regency"]);
							?>
							<td><?php echo $regName; ?></td>
							<td><?php echo $subDis["sub_district"]; ?></td>
							<td><?php echo $subDis["price_shipping_reguler"]; ?></td>
							<td><?php echo $subDis["price_shipping_ok"]; ?></td>
						</tr>
						<?php } ?>
					</table>
					<table>
						<tr>
							<td>
								<form action = "hummel_controller.php" method = "post">
									<input type = "hidden" name = "index" value = "<?php echo ((int)$_GET["index"]) - 10; ?>">
									<input type = "hidden" name = "input" value = "viewShip">
									<?php if ((int)$_GET["index"] == 0) { ?>
									<input type = "submit" value = "Previous" disabled>
									<?php } 
									else{ //if exactly 10 but after this no more data??
									?><input type = "submit" value = "Previous">
									<?php } ?>
								</form>
							</td>
							<td>
								<form action = "hummel_controller.php" method = "post">
									<input type = "hidden" name = "index" value = "<?php echo ((int)$_GET["index"]) + 10; ?>">
									<input type = "hidden" name = "input" value = "viewShip">
									<?php if (sizeof($subDisArray) - 1 < 10 || getSubDistrictRows() - (sizeof($subDisArray) - 1) == (int)$_GET["index"]) { ?>
									<input type = "submit" value = "Next" disabled>
									<?php } 
									else{ //if exactly 10 but after this no more data??
									?><input type = "submit" value = "Next">
									<?php } ?>
								</form>
							</td>
						</tr>
					</table>
					<?php 
					}
				}
			}
		}
		?> 
		<br><hr>
		<h1>Click the button below to manage your products</h1>
		<?php 
			//$_SESSION["firstIndex"] = 0;
			//$_SESSION["lastIndex"] = 5;
			$firstIndex = getProductRows();
		?>
		<form action = "hummel_controller.php" method = "post">
			<input type = "hidden" name = "firstIndex" value = "<?php echo $firstIndex-5; ?>">
			<input type = "hidden" name = "lastIndex" value = "5">
			<input type = "hidden" name = "input" value = "viewProduct">
			<input type = "submit" value = "Manage products">
		</form>
		<hr>
		<h1>Click the button below to visit the transaction section</h1>
		<?php 
			$firstMessage = getMessageRows();
		?>
		<form action = "hummel_controller_2.php" method = "post">
			<input type = "hidden" name = "firstMessage" value = "<?php echo $firstMessage-5; ?>">
			<input type = "hidden" name = "input" value = "viewMessage">
			<input type = "submit" value = "Go to transaction page">
		</form>
		<br><br><hr>
		<form action = "hummel_controller_2.php" method = "post">
			<input type = "hidden" name = "input" value = "logoutAdmin">
			<input type = "submit" value = "Log Out">
		</form>
		<?php
	}
?>