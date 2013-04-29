<?php session_start();
include("hummel_model.php");
$regencies = "";
if(!isset($_GET["regency"])){

}
else if(isset($_GET["regency"])){
	$regencies = $_GET["regency"];
	//echo $regencies;
}
$regArray = getAllRegency();
$subArray = getAllSubDistrict($regencies);

	if(!isset($_SESSION["loginArray"]) || !isset($_SESSION["cartName"])){
		//do nothing
		echo "Cannot access page";
	}
	else if(isset($_SESSION["loginArray"]) && isset($_SESSION["cartName"]) && isset($_GET["firstIndex"]) && isset($_GET["lastIndex"]) && isset($_GET["allPrice"])){
		$firstIndex = $_GET["firstIndex"];
		$lastIndex = $_GET["lastIndex"];
		$allPrice = $_GET["allPrice"];
		echo "<h1>Where do you want to ship these items</h1>";
		$loginArray = $_SESSION["loginArray"];
		$address = "";
		$postalCode = "";
		$phoneNumber = "";
		$regency = "";
		$subDistrict = "";
				
		for($i = 0; $i < sizeof($loginArray)-1; $i++){
			$login = $loginArray[$i];
			$address = $login["address"];
			$postalCode = $login["postal_code"];
			$phoneNumber = $login["phone_number"];
			$regency = $login["regency"];
			$subDistrict = $login["sub_district"];
		}
		?>
		<!-- If the address is the same, no need to store the things like address postal code etc in the json -->
		<form action = "hummel_controller.php" method = "post">
		<table>
			<tr>
				<td>
					<!-- Do not put the address stuff into json, just take it from database -->
					<h3>Ship to the address below</h3>
					<table>
						<tr>
							<td><B><U>Regency</U></B></td>
							<td><?php echo "<b>".$regency."</b>"; ?></td>
						</tr>
						<tr>
							<td><B><U>Sub-District</U></B></td>
							<td><?php echo "<b>".$subDistrict."</b>"; ?></td>
						</tr>
						<tr>
							<td colspan = "2"><hr></td>
						</tr>
						<tr>
							<td>Address</td>
							<td><?php echo "<b>".$address."</b>"; ?></td>
						</tr>
						<tr>
							<td>Postal Code</td>
							<td><?php echo "<b>".$postalCode."</b>"; ?></td>
						</tr>
						<tr>
							<td>Phone Number</td>
							<td><?php echo "<b>".$phoneNumber."</b>"; ?></td>
						</tr>
						<tr>
							<td>Choose delivery service</td>
							<td><select name = "delService">
								<option>Package Reguler</option>
								<option>Package OK</option>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td>
					<!-- <input type = "hidden" name = "input" value = "shipSameAddress"> -->
					<input type = "hidden" name = "allPrice" value = "<?php echo $allPrice; ?>">
					<input type = "hidden" name = "address" value = "<?php echo $address; ?>">
					<input type = "hidden" name = "postalCode" value = "<?php echo $postalCode; ?>">
					<input type = "hidden" name = "phoneNumber" value = "<?php echo $phoneNumber; ?>">
					<input type = "hidden" name = "regency" value = "<?php echo $regency; ?>">
					<input type = "hidden" name = "subDistrict" value = "<?php echo $subDistrict; ?>">
					<input type = "hidden" name = "firstIndex" value = "<?php echo $firstIndex; ?>">
					<input type = "hidden" name = "lastIndex" value = "<?php echo $lastIndex; ?>">
					<input type = "hidden" name = "input" value = "shipSameAddress">
					<input type = "submit" value = "Go to confirmation page">
				</td>
			</tr>
		</table>
		</form>
		<br><br><br>
		<form action = "hummel_controller.php" method = "post">
		<table>
			<tr>
				<td>
					<!-- Put the address stuff into json, just take it from database -->
					<h3>Ship to another address</h3>
					<table>
						<tr>
							<td><b>Please specify first the regency which becomes the shipping address</b></td>
							<td>
								<select name = "regency1" id = "regSelect">
								<?php 
								foreach($regArray as $reg){
									echo "<option>".$reg."</option>";
								}
								?>
								</select>
							</td>
						</tr>
						<script type = "text/javascript">
							var regChosen = '<?php echo $regencies; ?>';
							if(regChosen == ''){
							}
							else if(regChosen != ''){
								var regSels = document.getElementById("regSelect");
								regSels.value = regChosen;
							}
						</script>
						<script type = "text/javascript">
							var regSels = document.getElementById("regSelect").onchange = function(){
							var regency = this.options[this.selectedIndex].text;
							window.location.href = "shipping.php?regency="+regency+"&firstIndex="+"<?php echo $firstIndex; ?>"+"&lastIndex="+"<?php echo $lastIndex; ?>"+"&allPrice="+"<?php echo $allPrice; ?>";
							//this.value = strUser;
							//document.write(strUser); //VALUE CAN BE TAKEN ACTUALLY
							};
						</script>
						<tr>
							<td><b>Now please choose the sub district of the shipping address</b></td>
							<td>
								<select name = "subDistrict1">
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
							<td>Input the address</td>
							<td><input type = "text" name = "address1"></td>
						</tr>
						<tr>
							<td>Input the postal code</td>
							<td><input type = "text" name = "postalCode1"></td>
						</tr>
						<tr>
							<td>Input the phone number</td>
							<td><input type = "text" name = "phoneNumber1"></td>
						</tr>
						<tr>
							<td>Choose delivery service</td>
							<td><select name = "delService1">
								<option>Package Reguler</option>
								<option>Package OK</option>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td>
					<input type = "hidden" name = "allPrice" value = "<?php echo $allPrice; ?>">
					<input type = "hidden" name = "firstIndex" value = "<?php echo $firstIndex; ?>">
					<input type = "hidden" name = "lastIndex" value = "<?php echo $lastIndex; ?>">
					<input type = "hidden" name = "input" value = "shipDifAddress">
					<input type = "submit" value = "Go to confirmation page">
				</td>
			</tr>
		</table>
		</form>
		<br><br><br>
		
		<?php 
			if(!isset($_GET["message"])){
				//do nothing
			}
			else if(isset($_GET["message"])){
				$message = $_GET["message"];
				
				if($message == "empty_field"){
					echo "<font color = red>We are sorry. It seems you have chosen to ship the items to another address, but there is some data that you have not yet filled. Please fill it first</font>";
				}
			}
		?>
		<hr>
		<form action = "index.php" method = "get">
			<input type = "hidden" name = "firstIndex" value = "<?php echo $firstIndex; ?>">
			<input type = "hidden" name = "lastIndex" value = "<?php echo $lastIndex; ?>">
			<input type = "submit" value = "Back to Product Page">
		</form>
		<?php
	}
	else{
	echo "FAILAAAAAAAAAA";
	}
?>