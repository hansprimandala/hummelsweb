<?php session_start();
include("hummel_model.php");
	if(isset($_GET["id"]) && isset($_SESSION["jsonProductDetail"]) && isset($_GET["first"]) && isset($_GET["last"])){
			//echo "id is ".$_GET["id"];
			?>
			<html><head>
				<script = "text/javascript">
					function validateTotal(total){
						if(isNaN(total)){
							alert('the number to be bought must be in number!');
							return false;
						}
						return true;
					}
				</script>
			</head>
			<?php
			$jsonProduct = $_SESSION["jsonProductDetail"];
			$productArray = json_decode($jsonProduct,true);
			
			$firstIndex = $_GET["first"];
			$lastIndex = $_GET["last"];
				
			if($productArray[0] == false){
				echo "No details to see";
			}
			else{
				$productStock = 0;
				$id = 0;
				$priceItem = 0;
				for($i = sizeof($productArray)-2; $i >=0; $i--){
					$product = $productArray[$i]; 
					$id = $product["idproduct"];
					
					if($id == (int)$_GET["id"]){
						$id = $product["idproduct"];
						$title = $product["product_photo_title"]; //correct already
						$productStock = $product["product_stock"];
						$priceItem = $product["product_price"];
						echo "Product Name: ".$product["product_name"]."<br>";
						echo "Product Details: ".$product["product_details"]."<br>";
						echo "Product Price: ".$product["product_price"]."<br>";
						echo "Product Stock: ";
						if($product["product_stock"] == 0){
							echo "<font color = red size = 20>Out of Stock!</font><br>";
						}
						else{
							echo $product["product_stock"]."<br>";
						}
						echo "<td><img src = Product_Photo/$title width = 300 height = 300></td>";
						
						if(!isset($_GET["message"])){
							//do nothing
						}
						else if(isset($_GET["message"])){
							$message = $_GET["message"];
							
							if($message == "empty_field"){
								echo "<font color = red>Fields must not be empty!</font>";
							}
						}
						break;
					}
				}
				
				if(!isset($_SESSION["loginArray"])){
					//do nothing
				}
				else if(isset($_SESSION["loginArray"])){
					//echo "<br><br><a href = hummel_controller.php?input=addCart>Add to Cart</a>";
					?>
					<form action = "hummel_controller.php" method = "post" onKeyup = "validateTotal(this.totalBuy.value)">
						<?php if($productStock == 0){
							echo "<font color = red>You cannot buy the corresponding item because the stock is zero</font>"; //the filter should exist
						} 
						else{
						?>
						How many of this corresponding that you want to buy?
						<table>
							<tr>
								<td><input type = "text" name = "totalBuy"></td>
								<td>
									<input type = "hidden" name = "firstIndex" value = "<?php echo $firstIndex; ?>">
									<input type = "hidden" name = "lastIndex" value = "<?php echo $lastIndex; ?>">
									<input type = "hidden" name = "itemPrice" value = "<?php echo $priceItem; ?>">
									<input type = "hidden" name = "id" value = "<?php echo $id; ?>">
									<input type = "hidden" name = "input" value = "addCart">
									<input type = "submit" value = "Add to Cart">
								</td>
							</tr>
						</table>
						<?php  } ?>
					</form>
					<?
					
					if(!isset($_GET["message"])){
						//do nothing
					}
					else if(isset($_GET["message"])){
						$message = $_GET["message"];
						
						if($message == "no_stock"){
							echo "<font color = red>The stock is suddenly zero. There might be other person who shops at the same time with you<br>
							We are sorry for this inconvenience
							</font>";
						}
						else if($message == "stock_not_enough"){
							echo "<font color = red>The stock is not enough. We are sorry for this inconvenience</font>";
						}
						else if($message == "added_cart"){
							echo "<font color = red>This item has been added your cart</font>";
						}
						else if($message == "empty_field"){
							echo "<font color = red>The field must not be empty</font>";
						}
						else if($message == "buy_zero"){
							echo "<font color = red>Sorry, you cannot input zero or less than zero for buying the items</font>";
						}
					}
				}
			}
		}
		else{
			//echo "bbb";
		}
		//$firstIndex = getProductRows();
		?>
		<form action = "index.php" method = "get">
			<input type = "hidden" name = "firstIndex" value = "<?php echo $firstIndex; ?>">
			<input type = "hidden" name = "lastIndex" value = "<?php echo $lastIndex; ?>">
			<input type = "submit" value = "Back">
		</form>
		
		<form action = "checkCart.php" method = "get">
			<input type = "hidden" name = "firstIndex" value = "<?php echo $firstIndex; ?>">
			<input type = "hidden" name = "lastIndex" value = "<?php echo $lastIndex; ?>">
			<input type = "submit" value = "Check your cart">
		</form>
		<?php
?>