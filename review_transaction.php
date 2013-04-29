<?php session_start(); 
include("hummel_model.php");
?>
<html>
	<head><title>Review Your Items</title></head>
	<body>
		<?php 
		if(isset($_GET["cartName"])){
			$cartName = $_GET["cartName"];		
			?>
			<form action = "hummel_controller_2.php" method = "post">
				<h1>Please input your e-mail and password first</h1>
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
						<td><input type = "hidden" name = "input" value = "reviewItem">
							<input type = "hidden" name = "cartName" value = "<?php echo $cartName; ?>">
						</td>
						<td><input type = "submit" value = "Log In"></td>
					</tr>
				</table>
			</form>	
			<?php
			
			if(isset($_SESSION["cartArray"])){ //set it in login again
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
					$cartArray = $_SESSION["cartArray"];
					
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
				unset($_SESSION["cartArray"]);	
			}
			
			if(isset($_GET["message"])){
				$message = $_GET["message"];
				
				if($message == "login_empty"){
					echo "<font color = red>All fields must be filled in order to proceed to log-in</font>";
				}
				else if($message == "login_fail"){
					echo "<font color = red>Your e-mail address or password might be wrong</font>";
				}
			}
		}
		?>
	</body>
</html>