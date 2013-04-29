<?php session_start();
include("hummel_model.php");
	if(!isset($_SESSION["jsonLogin"])){
		header("Location:admin_index.php?message=cannot_jump_admin");
	}
	else if(isset($_SESSION["jsonLogin"])){
		$firstMessage = 0;
		$totalView = 0;
		if(isset($_SESSION["messageArray"])){
			if(isset($_GET["firstMessage"]) && isset($_GET["totalView"])){
				$firstMessage = $_GET["firstMessage"];
				$totalView = $_GET["totalView"];
				$messageArray = $_SESSION["messageArray"];
				//echo "HAI ".$messageArray[0];
				?>
				<table border = "1">
					<th>No.Message</th>
					<th>Confirmation Code</th>
					<th>Status Message</th>
					<th>Status Shipping</th>
					<?php 
					for($i = sizeof($messageArray)-2; $i >=0; $i--){
						$message = $messageArray[$i]; 
						$id = $message["idconfirmation_message"];
						$confirmCode = $message["confirmation_code"]; //correct already
						echo "<tr>";
						echo "<td>".$id."</td>";
						if($message["status_message"] == "Unread"){
							echo "<td><a href = hummel_controller_2.php?input=getMessageDetail&id=$id&firstMessage=$firstMessage&totalView=$totalView><b><i>".$confirmCode."</i></b></a></td>"; //must be in A href for showing more details, UPDATE AND DELETE!
						}
						else{
							echo "<td><a href = hummel_controller_2.php?input=getMessageDetail&id=$id&firstMessage=$firstMessage&totalView=$totalView>".$confirmCode."</a></td>";
						}
						echo "<td>".$message["status_message"]."</td>";
						echo "<td>".$message["status_shipping"]."</td>";
						echo "</tr>";
						//echo "<img src = Product_Photo/arcreactor.jpg>";
					}
					?>
				</table>
				<?php
					
				?>
				<form action = "hummel_controller_2.php" method = "post">
					<table>
						<tr>
							<td>
								<input type = "hidden" name = "firstMessage" value = "<?php echo $firstMessage + 5;?>"> <!-- Simply echo it! To test, use input type text first -->
								<input type = "hidden" name = "input" value = "viewMessage">
								<?php if($firstMessage == getMessageRows()-5){ ?>
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
				<form action = "hummel_controller_2.php" method = "post">
					<table>
						<tr>
							<td>
								<input type = "hidden" name = "firstMessage" value = "<?php echo $firstMessage - 5;?>"> <!-- Simply echo it! To test, use input type text first -->
								<input type = "hidden" name = "input" value = "viewMessage">
								<?php if ($firstMessage <= 0){?>
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
				<?php
			}
		}
		else if(!isset($_SESSION["messageArray"])){
			echo "<font color = red>You cannpt access the message. Please access the page by clicking the button at the previous page</font>";
		}
		?>
		
		<h1>Check unconfirmed cart that has passed more than 3 days</h1>
		<form action = "hummel_controller_2.php" method = "post">
			<input type = "hidden" name = "firstMessage" value = "<?php echo $firstMessage; ?>">
			<input type = "hidden" name = "totalView" value = "<?php echo $totalView; ?>">
			<input type = "hidden" name = "input" value = "check3DaysCart">
			<input type = "submit" value = "Check and Remove Cart">
		</form>
		
		<h1>Remove items from confirmed cart (exceed 3 days without payment being accomplished)</h1>
		<form action = "hummel_controller_2.php" method = "post">
			<input type = "hidden" name = "firstMessage" value = "<?php echo $firstMessage; ?>">
			<input type = "hidden" name = "totalView" value = "<?php echo $totalView; ?>">
			<input type = "hidden" name = "input" value = "check3DaysConfirmed">
			<input type = "submit" value = "Check and Remove Confirmed Transaction">
		</form>
		
		<h1>Track Shipped Item Details</h1>
		<form action = "hummel_controller_2.php" method = "post">
			<table border = "1">
				<tr>
					<td>Input the shipping code</td>
					<td><input type = "text" name = "shipCode"></td>
				</tr>
				<tr>
					<td>
						<input type = "hidden" name = "firstMessage" value = "<?php echo $firstMessage; ?>">
						<input type = "hidden" name = "totalView" value = "<?php echo $totalView; ?>">
						<input type = "hidden" name = "input" value = "checkItemsShipped">
					</td>
					<td>
						<input type = "submit" value = "Check Shipped Items">
					</td>
				</tr>
			</table>
		</form>
		
		<?php 
			if(isset($_SESSION["itemArray"])){
				$itemArray = $_SESSION["itemArray"];
				unset($_SESSION["itemArray"]);
				
				if($itemArray[0] == false){
					echo "The shipping code might be wrong";
				}
				else{
					echo "<table border = 1>";
					echo "<th>Item Name</th>";
					echo "<th>Total Bought</th>";
					echo "<th>Total Price</th>";
					for($i = 0; $i < sizeof($itemArray) - 1; $i++){
						$item = $itemArray[$i];
						$idItem = $item["id_item"];
						$itemName = getItemName($idItem);
						echo "<tr>";
						echo "<td>".$itemName."</td>";
						echo "<td>".$item["total_bought"]."</td>";
						echo "<td>".$item["total_price"]."</td>";
						echo "</tr>";
					}
					echo "</table>";
				}
			}
		
		?>
		
		<?php 
			if(!isset($_GET["message"])){}
			else if(isset($_GET["message"])){
				$message = $_GET["message"];
				
				if($message == "3days_deleted"){
					echo "<font color = red>The items inside the unconfirmed cart that has exceeded 3 days have been removed</font>";
				}
				else if($message == "3daysconfirm_deleted"){
					echo "<font color = red>The items inside the confirmed cart (exceed 3 days) have been removed</font>";
				}
				else if($message == "empty_field"){
					echo "<font color = red>The field(s) must not be empty</font>";
				}
			}
		?>
		<br><br><hr>
		<?php 	
			$firstMessage = getMessageRows();
		?>
		<form action = "hummel_controller_2.php" method = "post">
			<input type = "hidden" name = "firstMessage" value = "<?php echo $firstMessage-5; ?>">
			<input type = "hidden" name = "input" value = "viewMessage">
			<input type = "submit" value = "Refresh Page">
		</form>
		
		<form action = "admin_page.php">
			<input type = "submit" value = "Back">
		</form>
		<?php
	}
?>