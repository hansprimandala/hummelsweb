<?php session_start(); 
include("hummel_model.php");
	if(!isset($_SESSION["loginArray"]) || !isset($_SESSION["cartName"])){
		//do nothing
		echo "<font color = red>You have not yet any cart yet.<br>
		Please add at least one item to the cart so thatw e can give you a cart, or choose from the cart menu at the front page
		</font>";
	}
	else if(isset($_SESSION["loginArray"]) && isset($_SESSION["cartName"]) && isset($_GET["firstIndex"]) && isset($_GET["lastIndex"])){
		?>
		<html><head>
			<script = "text/javascript">
				function validateTotal(total){
					if(isNaN(total)){
						alert('the number to be updated must be in number!');
						return false;
					}
					return true;
				}
			</script>
		</head>
		<?php
		$firstIndex = $_GET["firstIndex"];
		$lastIndex = $_GET["lastIndex"];
		$allPrice = 0;
		?> 
		<h1>This is your shopping cart</h1>
		<table border = "1">
			<th>Item name</th>
			<th>Total bought</th>
			<th>Total price</th>
			<th>Date added</th>
			<th>Update</th>
			<th>Remove</th>
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
					?>
					<td>
						<form action = "hummel_controller.php" method = "post" onKeyup ="validateTotal(this.totalUpdate.value)">
							<table>
								<tr>
									<td>Input the final number of items you want to buy</td>
								</tr>
								<tr>
									<td><input type = "text" name = "totalUpdate"></td>
								</tr>
								<tr>
									<td>
										<input type = "hidden" name = "firstIndex" value = "<?php echo $firstIndex; ?>">
										<input type = "hidden" name = "lastIndex" value = "<?php echo $lastIndex; ?>">
										<input type = "hidden" name = "totalBought" value = "<?php echo $cart["total_bought"]; ?>">
										<input type = "hidden" name = "totalPrice" value = "<?php echo $cart["total_price"]; ?>">
										<input type = "hidden" name = "idItem" value = "<?php echo $idItem; ?>">
										<input type = "hidden" name = "input" value = "updateCart">
										<input type = "submit" value = "Update Cart">
									</td>
								</tr>
							</table>
						</form>
					</td>
					<?php
					$allPrice = $allPrice + $cart["total_price"];
					echo "<td><a href = hummel_controller.php?input=removeCart&idItem=$idItem&totalStock=$cart[total_bought]&firstIndex=$firstIndex&lastIndex=$lastIndex>Remove</a></td>";
					echo "</tr>";
				}
				
				echo "<B>Total price for all items in the shopping cart is <I>".$allPrice."</I></B>";
			}
			?>
			</table>
			<?php
				//now create clear cart option
				if(getCartDetail($_SESSION["cartName"]) != 0){
				?>
					<form action = "hummel_controller.php" method = "post">
						<input type = "hidden" name = "firstIndex" value = "<?php echo $firstIndex; ?>">
						<input type = "hidden" name = "lastIndex" value = "<?php echo $lastIndex; ?>">
						<input type = "hidden" name = "input" value = "clearCart">
						<input type = "submit" value = "Clear Cart">
					</form>
				<?php
				}
				else if(getCartDetail($_SESSION["cartName"]) == 0){
				?>
					<form action = "hummel_controller.php" method = "post">
						<input type = "hidden" name = "firstIndex" value = "<?php echo $firstIndex; ?>">
						<input type = "hidden" name = "lastIndex" value = "<?php echo $lastIndex; ?>">
						<input type = "hidden" name = "input" value = "clearCart">
						<input type = "submit" value = "Clear Cart" disabled>
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
				
				if($message == "no_stock"){
					echo "<font color = red>Sorry, we are out ouf stock for this item. We really are sorry for this inconvenience</font>";
				}
				else if($message == "stock_not_enough"){
					echo "<font color = red>Sorry, the stock is not enough and thus the update process cannot be done</font>";
				}
				else if($message == "updated_cart"){
					echo "<font color = red>Your cart has been updated</font>";
				}
				else if($message == "removed_cart"){
					echo "<font color = red>The item that you choose has been removed from your cart</font>";
				}
				/* else if($message == "cleared_cart"){
					echo "<font color = red>Your cart has been cleared</font>";
				}
				else if($message == "no_cart"){
					echo "<font color = red>Your cart does not exist anymore. Please shop once again to create your cart</font>";
				}*/
			}
			?>
		<br><br>
		<?php if(getCartDetail($_SESSION["cartName"]) != 0){ ?>
			<form action = "shipping.php" method = "get">
				<input type = "hidden" name = "allPrice" value = "<?php echo $allPrice; ?>">
				<input type = "hidden" name = "firstIndex" value = "<?php echo $firstIndex; ?>">
				<input type = "hidden" name = "lastIndex" value = "<?php echo $lastIndex; ?>">
				<input type = "submit" value = "Go to shipping management">
			</form>
		<?php }
		else if(getCartDetail($_SESSION["cartName"]) == 0){
		?>
			<form action = "shipping.php" method = "get">
				<input type = "hidden" name = "firstIndex" value = "<?php echo $firstIndex; ?>">
				<input type = "hidden" name = "lastIndex" value = "<?php echo $lastIndex; ?>">
				<input type = "submit" value = "Go to shipping management" disabled>
			</form>
		<?php
		}
		?>
		
		<form action = "index.php" method = "get">
			<input type = "hidden" name = "firstIndex" value = "<?php echo $firstIndex; ?>">
			<input type = "hidden" name = "lastIndex" value = "<?php echo $lastIndex; ?>">
			<input type = "submit" value = "Back to Product Page">
		</form>
		<?php
	}
?>