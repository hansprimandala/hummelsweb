<?php session_start();
include("hummel_model.php");
	if(!isset($_SESSION["jsonLogin"])){
		header("Location:admin_index.php?message=cannot_jump_admin");
	}
	else if(isset($_SESSION["jsonLogin"])){
		?>
		<html><head>
			<script = "text/javascript">
				function validatePriceStock(stock, price){
					if(isNaN(stock) || isNaN(price)){
						alert('price or stock number must be in number!');
						return false;
					}
					return true;
				}
			</script>
		</head>
		<h1>View your products</h1>
		<?php 
			if(!isset($_SESSION["jsonProduct"])){
				echo "You haven't added a single product yet!";
			}
			else if(isset($_SESSION["jsonProduct"])){
				$jsonProduct = $_SESSION["jsonProduct"];
				$productArray = json_decode($jsonProduct,true);
				
				if($productArray[0] == false){
					echo "No more products to see";
				}
				else{
					if(isset($_GET["first"]) && isset($_GET["last"])){
					$firstIndex = $_GET["first"];
					$lastIndex = $_GET["last"];
					//echo "first is ".$_GET["first"];
					//echo "last is ".$_GET["last"]; //so these two are correct!!
					?>
					<table>
						<?php 
						for($i = sizeof($productArray)-2; $i >=0; $i--){
							$product = $productArray[$i]; 
							$id = $product["idproduct"];
							$title = $product["product_photo_title"]; //correct already
							echo "<tr>";
							echo "<td><a href = hummel_controller.php?input=getProdDetail&id=$id&first=$firstIndex&last=$lastIndex>".$product["product_name"]."</a></td>"; //must be in A href for showing more details, UPDATE AND DELETE!
							echo "<td><img src = Product_Photo/$title width = 100 height = 100></td>";
							echo "</tr>";
							//echo "<img src = Product_Photo/arcreactor.jpg>";
						}
						?>
					</table>
					<?php
					
					?>
					<form action = "hummel_controller.php" method = "post">
						<table>
							<tr>
								<td>
									<input type = "hidden" name = "firstIndex" value = "<?php echo $firstIndex + 5;?>"> <!-- Simply echo it! To test, use input type text first -->
									<input type = "hidden" name = "lastIndex" value = "<?php echo $lastIndex;?>">
									<input type = "hidden" name = "input" value = "viewProduct">
									<?php if($firstIndex == getProductRows()-5){ ?>
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
					<form action = "hummel_controller.php" method = "post">
						<table>
							<tr>
								<td>
									<input type = "hidden" name = "firstIndex" value = "<?php echo $firstIndex - 5;?>"> <!-- Simply echo it! To test, use input type text first -->
									<input type = "hidden" name = "lastIndex" value = "<?php echo $lastIndex;?>">
									<input type = "hidden" name = "input" value = "viewProduct">
									<?php if ($firstIndex <= 0){?>
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
			}
		?>
		
		<?php
			if(!isset($_SESSION["productName"])){
				//simply do nothing
			}
			else if(isset($_SESSION["productName"])){
				$productName =  $_SESSION["productName"];
				unset($_SESSION["productName"]);
				echo "<font color = red>".$productName." has been removed</font>";
			}
		?>
		<h1>Add products</h1>
		<form action = "hummel_controller.php" method = "post" enctype="multipart/form-data" onKeyup = "validatePriceStock(this.productStock.value, this.productPrice.value)">
			<table>
				<tr>
					<td>Product Name</td>
					<td><input type = "text" name = "productName"></td>
				</tr>
				<tr>
					<td>Product Detail</td>
					<td><textarea name = "productDetail" width = "300" height = "300"></textarea></td>
				</tr>
				<tr>
					<td>Product Price</td>
					<td><input type = "text" name = "productPrice"></td>
				</tr>
				<tr>
					<td>Product Stock</td>
					<td><input type = "text" name = "productStock"></td>
				</tr>
				<tr>
					<td>Upload Product Photo</td>
					<td><input type = "file" name = "productImage"></td>
				</tr>
				<tr>
					<td><input type = "hidden" name = "input" value = "addProduct"></td>
					<td><input type = "submit" name = "Submit" value = "Add Products!"></td>
				</tr>
			</table>
		</form><br>
		<hr>
		<?php
		if(!isset($_GET["message"])){
			//do nothing
		}
		else if(isset($_GET["message"])){
			$message = $_GET["message"];
			
			if($message == "empty_field"){
				echo "<font color = red>Fields must not be empty!</font>";
			}
			else if($message == "price_error"){
				echo "<font color = red>Price must be in number!</font>";
			}
			else if($message == "stock_error"){
				echo "<font color = red>Stock must be in number!</font>";
			}
			else if($message == "no_picture"){
				echo "<font color = red>No photo has been chosen</font>";
			}
			else if($message == "image_existed"){
				echo "<font color = red>Sorry, but the name for this item has been used. Please pick another name</font>";
			}
		}
		?>
		<form action = "admin_page.php" method = "post">
			<input type = "submit" value = "Back">
		</form>
		<?php
	}
?>