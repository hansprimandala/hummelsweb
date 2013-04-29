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
		<?php
		if(isset($_GET["id"]) && isset($_SESSION["jsonProductDetail"]) && isset($_GET["first"]) && isset($_GET["last"])){
			//echo "id is ".$_GET["id"];
			$jsonProduct = $_SESSION["jsonProductDetail"];
			$productArray = json_decode($jsonProduct,true);
			
			$firstIndex = $_GET["first"];
			$lastIndex = $_GET["last"];
				
			if($productArray[0] == false){
				echo "No details to see";
			}
			else{
				for($i = sizeof($productArray)-2; $i >=0; $i--){
					$product = $productArray[$i]; 
					$id = $product["idproduct"];
					
					if($id == (int)$_GET["id"]){
						$id = $product["idproduct"];
						$title = $product["product_photo_title"]; //correct already
						echo "<a href = hummel_controller.php?input=removeProduct&id=$id&first=$firstIndex&last=$lastIndex&title=$title>Remove This Product</a><br><br>";
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
						
						//Update command below:
						?>
						<br><br>
						<form action = "hummel_controller.php" method = "post" enctype="multipart/form-data" onKeyup = "validatePriceStock(this.productStock.value, this.productPrice.value)">
							<table>
								<tr>
									<td>Product Name</td>
									<td><input type = "text" name = "productName" value = "<?php echo $product["product_name"];?>"></td>
								</tr>
								<tr>
									<td>Product Detail</td>
									<td><textarea name = "productDetail" width = "300" height = "300"><?php echo $product["product_details"];?></textarea></td>
								</tr>
								<tr>
									<td>Product Price</td>
									<td><input type = "text" name = "productPrice" value = "<?php echo $product["product_price"];?>"></td>
								</tr>
								<tr>
									<td>Product Stock</td>
									<td><input type = "text" name = "productStock" value = "<?php echo $product["product_stock"];?>"></td>
								</tr>
								<tr>
									<td>Upload Product Photo</td>
									<td><input type = "file" name = "productImage"></td>
								</tr>
								<tr>
									<td><input type = "hidden" name = "input" value = "updateProduct">
									<input type = "hidden" name = "firstIndex" value = "<?php echo $firstIndex; ?>">
									<input type = "hidden" name = "lastIndex" value = "<?php echo $lastIndex; ?>">
									<input type = "hidden" name = "id" value = "<?php echo $id; ?>">
									<input type = "hidden" name = "title" value = "<?php echo $product["product_photo_title"]; ?>">
									</td>
									<td><input type = "submit" name = "Submit" value = "Update Products!"></td>
								</tr>
							</table>
						</form>
						<?php
						//Remove command below:
						if(!isset($_GET["message"])){
							//do nothing
						}
						else if(isset($_GET["message"])){
							$message = $_GET["message"];
							
							if($message == "empty_field"){
								echo "<font color = red>Fields must not be empty!</font>";
							}
							else if($message == "update_fail"){
								echo "<font color = red>Update fail! The name chosen for the update might be the same with the other products</font>";
							}
						}
						break;
					}
				}
			}
		}
		//$firstIndex = getProductRows();
		?>
		<form action = "hummel_controller.php" method = "post">
			<input type = "hidden" name = "firstIndex" value = "<?php echo $firstIndex; ?>">
			<input type = "hidden" name = "lastIndex" value = "<?php echo $lastIndex; ?>">
			<input type = "hidden" name = "input" value = "viewProduct">
			<input type = "submit" value = "Back">
		</form>
		<?php
	}
?>