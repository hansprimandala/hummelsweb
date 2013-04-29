<?php session_start();
include("hummel_model.php");
	if(!isset($_SESSION["loginArray"])){
		echo "<font color = red>Sorry, this page cannot be accessed. You have to log in first</font>";
	}
	else if(isset($_SESSION["loginArray"])){
		$idUser = 0;
		for($i = 0; $i < sizeof($_SESSION["loginArray"]) - 1; $i++){
			$login = $_SESSION["loginArray"][$i];
			$idUser = $login["iduser"];
		}
		if(isset($_SESSION["cartDataArray"])){
			$cartDataArray = $_SESSION["cartDataArray"];
			
			//loop the array
			echo "<table border = 1>";
			echo "<th>Description</th>";
			echo "<th>Shopping Cart Name</th>";
			echo "<th>Remove Operation</th>";
			for($i = 0; $i < sizeof($cartDataArray)-1; $i++){
				$cart = $cartDataArray[$i];
				
				//while looping, check if the id of the shopping cart is available in table confirmation table or not, if yes, don't make it as a href
				$idCart = $cart["idshopping_cart"];
				$match = matchCartWithConfirm($idCart);
				$cartName = $cart["shopping_cart_name"];
				
				if($match == false){
					//click to view the items
					echo "<tr>";
					echo "<td>Confirmed Cart</td>";
					echo "<td>Click to view the items in <a href = cartAvail.php?cartName=$cartName>$cartName</a></td>";
					echo "<td>Confirmed Transaction cannot be removed</td>";
					echo "</tr>";
				}
				else if($match == true){
					//click to set session cart
					echo "<tr>";
					echo "<td>Unconfirmed Cart</td>";
					echo "<td>Click to view and load the <a href = hummel_controller.php?input=setCart&cartName=$cartName>$cartName</a></td>";
					echo "<td>Remove the cart <a href = hummel_controller.php?input=deleteCart&idCart=$idCart&idUser=$idUser>$cartName</a></td>";
					echo "</tr>";
				}
			}
			echo "</table>";
		}
		
		if(!isset($_GET["cartName"])){
		}
		else if(isset($_GET["cartName"])){
			$cartName = $_GET["cartName"];
			$allPrice = 0.0;
				?>
				<h1>This is your shopping cart</h1>
				<table border = "1">
					<th>Item name</th>
					<th>Total bought</th>
					<th>Total price</th>
					<th>Date added</th>
					<?php 		
					//retrieve the cart data
					$jsonCart = viewCart($_GET["cartName"]);
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
							$allPrice = $allPrice + $cart["total_price"];
							echo "</tr>";
						}
						
						echo "<B>Total price for all items in the shopping cart is <I>".$allPrice."</I></B>";
					}
					?>
					</table>
				<?php
		}
		
		if(!isset($_SESSION["cartName"])){
			echo "<font color = red>Your current cart has been removed</font>";
		}
		else if(isset($_SESSION["cartName"])){
			$cartName = $_SESSION["cartName"];
			$allPrice = 0.0;
				?>
				<h1>This is current loaded your shopping cart</h1>
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
							$allPrice = $allPrice + $cart["total_price"];
							echo "</tr>";
						}
						
						echo "<B>Total price for all items in the shopping cart is <I>".$allPrice."</I></B>";
					}
					?>
					</table>
				<?php
		}
		
		if(!isset($_GET["message"])){
		}
		else if(isset($_GET["message"])){
			$message = $_GET["message"];
			
			if($message == "loaded_cart"){
				echo "<font color = red>Your previous cart (that has not yet been confirmed) has been loaded</font>";
			}
			else if($message == "cart_destroyed"){
				echo "<font color = red>The chosen cart has been destroyed and can never be recovered anymore</font>";
			}
			else if($message == "current_cart_destroyed"){
				echo "<font color = red>Your current loaded cart has been destroyed and can never be recovered anymore</font>";
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