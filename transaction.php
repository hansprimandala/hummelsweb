<?php session_start();
include("hummel_model.php");
	if(!isset($_SESSION["loginArray"]) || !isset($_SESSION["cartName"])){
		//do nothing
		echo "<font color = red>Sorry, you cannot access this page</font>";
	}
	else if(isset($_SESSION["loginArray"]) && isset($_SESSION["cartName"])){
		if(isset($_SESSION["lastMessage"])){
			echo $_SESSION["lastMessage"];
			
			/* if(!isset($_GET["cartName"])){
			
			}
			else if(isset($_GET["cartName"])){
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
			} */
		}
		//unset($_SESSION["cartName"]);
		?>
		<form action = "index.php" method = "get">
			<input type = "hidden" name = "operation" value = "destroyCart">
			<input type = "submit" value = "Go back to main page">
		</form>
		<?php
	}
?>