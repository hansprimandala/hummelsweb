<?php 
	date_default_timezone_set("Asia/Jakarta");
		
		function openDatabaseConnection(){
			//$con = mysql_connect("localhost","hansprim_2806","hansos");
			$con = mysql_connect("localhost","root","");
		
			if(!$con){
				die("No connection is available!".mysql_error());
			}
		
			mysql_select_db("hummelsdb",$con);
			//mysql_select_db("hansprim_hummel",$con);
		
			return $con;
		}
		
		function loginAdmin($email,$password){ // void function
			$con = openDatabaseConnection();
			
			$loginQuery = mysql_query("SELECT * from admin WHERE email = '$email' && password = '$password'");
			$loginNum = mysql_num_rows($loginQuery);
			
			if($loginNum == 0 || $loginNum > 1){
				//echo "error_log_in";
				return "error_log_in";
			}
			else if($loginNum == 1){
				$loginArray = array();
				
				while($loginArray[] = mysql_fetch_assoc($loginQuery)){
					//putting all login value results into an array
				}
				
				$jsonLogin = json_encode($loginArray);
				return $jsonLogin;
			}
			
			mysql_close($con);
		}
		
		function storeShippingCost($regency,$subDistrict,$priceReg,$priceOK){
			$con = openDatabaseConnection();

			//searching number of value inside the corresponding table
			$rowQuery = mysql_query("SELECT * from shipping_regency");
			//$searchQuery = "";
			
			$rowNumber = mysql_num_rows($rowQuery);
			
			if($rowNumber == 0){ //no data yet, so imply input it to table
				//inputting regency into table shipping_regency
				mysql_query("INSERT INTO shipping_regency(regency) values ('$regency')");	
			}
			else{ //there is at least one row inserted to the table
				$searchQuery = mysql_query("SELECT * from shipping_regency ORDER BY (idshipping_regency) DESC");
				$regCompare = "";
				while($row = mysql_fetch_array($searchQuery)){
					$regCompare = $row["regency"];
					break;
				}
				
				if($regency == $regCompare){
					//do not insert the values anymore since it is still the same!
				}
				else if($regency != $regCompare){
					mysql_query("INSERT INTO shipping_regency(regency) values ('$regency')"); //new regency
				}
			}
			
			//while below will be used to take ID of each regency since we will need to put it to the database later (the table that consists of shipping cost)
			//$getIDReg = mysql_query("SELECT * from shipping_regency");
			
			$searchQueries = mysql_query("SELECT * from shipping_regency");
			$idReg = 0;
			while($row = mysql_fetch_array($searchQueries)){
				$idReg = $row["idshipping_regency"]; //definetely get 1 only, because each data is processed, not all silmutaenously
				//echo $idReg."<br>";
				//break;
			}
			//echo "SUBdISTRICT IS ".$subDistrict."<br>";
			//input all values into table shipping_payment_area
			//ERROR ESTIMATION: HERE!!!
			mysql_query("INSERT INTO shipping_payment_area(idshipping_regency,sub_district,price_shipping_reguler,price_shipping_ok) values ('$idReg','$subDistrict','$priceReg','$priceOK')");			
			mysql_close($con);
		}
		
		function removeShippingCost(){
			$con = openDatabaseConnection();
			mysql_query("DROP TABLE shipping_payment_area");
			mysql_query("DROP TABLE shipping_regency");
			//mysql_query("CREATE  TABLE IF NOT EXISTS `hummelsdb`.`shipping_regency` (`idshipping_regency` INT NOT NULL AUTO_INCREMENT ,`regency` TEXT NOT NULL ,PRIMARY KEY (`idshipping_regency`) ,UNIQUE INDEX `idshipping_regency_UNIQUE` (`idshipping_regency` ASC) )ENGINE = InnoDB");
			mysql_query("CREATE TABLE shipping_regency(idshipping_regency int AUTO_INCREMENT NOT NULL, regency TEXT NOT NULL, PRIMARY KEY(idshipping_regency))");
			mysql_query("CREATE TABLE shipping_payment_area(idshipping_payment_area int NOT NULL AUTO_INCREMENT, idshipping_regency int NOT NULL, sub_district text NOT NULL, price_shipping_reguler double NOT NULL, price_shipping_ok double NOT NULL, PRIMARY KEY(idshipping_payment_area), FOREIGN KEY(idshipping_regency) REFERENCES shipping_regency(idshipping_regency) ON UPDATE CASCADE ON DELETE CASCADE)");//, FOREIGN KEY(idshipping_regency) REFERENCES shipping_regency(idshipping_regency) ON UPDATE CASCADE ON DELETE CASCADE");
			mysql_close($con);		
		}
		
		function viewRegency($index,$total){
		    $con = openDatabaseConnection();
			$viewQuery = mysql_query("SELECT * from shipping_regency LIMIT $index,$total");
			$viewArray = array();
			
			while($viewArray[] = mysql_fetch_assoc($viewQuery)){}
			
			$jsonView = json_encode($viewArray);
			mysql_close($con);
			return $jsonView;
		}
		
		function viewSubdistrict($index,$total){
			$con = openDatabaseConnection();
			$viewQuery = mysql_query("SELECT * from shipping_payment_area LIMIT $index,$total");
			$viewArray = array();
			
			while($viewArray[] = mysql_fetch_assoc($viewQuery)){}
			
			$jsonView = json_encode($viewArray);
			mysql_close($con);
			return $jsonView;
		}
		
		function getProductRows(){
			$con = openDatabaseConnection();
			$rowQuery = mysql_query("SELECT * from product");
			$rowNumber = mysql_num_rows($rowQuery);
			mysql_close($con);
			return $rowNumber;
		}
		
		function getRegencyName($idReg){
			$con = openDatabaseConnection();
			$regQuery = mysql_query("SELECT * from shipping_regency WHERE idshipping_regency ='$idReg'");
			$regName = "";
			
			while($row = mysql_fetch_array($regQuery)){
				$regName = $row["regency"];
			}
			mysql_close($con);
			return $regName;
		}
		
		function getAllRegency(){
			$con = openDatabaseConnection();
			$regQuery = mysql_query("SELECT * from shipping_regency");
			$regArray = array();
			
			while($row = mysql_fetch_array($regQuery)){
				$regArray[] = $row["regency"];
			}
			mysql_close($con);
			return $regArray;
		}
		
		function getSubDistrictRows(){
			$con = openDatabaseConnection();
			$rowQuery = mysql_query("SELECT * from shipping_payment_area");
			$rowNumber = mysql_num_rows($rowQuery);
			mysql_close($con);
			return $rowNumber;
		}
		
		function getAllSubDistrict($regency){
			$con = openDatabaseConnection();
			$subArray = array();
			
			if($regency == ""){
				//means definetely takes idreg 1
				$subQuery = mysql_query("SELECT * from shipping_payment_area WHERE idshipping_regency = 1");
				while($row = mysql_fetch_array($subQuery)){
					$subArray[] = $row["sub_district"];
				}
			}
			else{ //not empty
				//get id regency
				$idQuery = mysql_query("SELECT * from shipping_regency WHERE regency ='$regency'");
				$idReg = 0;
				
				while($row = mysql_fetch_array($idQuery)){
					$idReg = $row["idshipping_regency"];
				}
				
				$subQuery = mysql_query("SELECT * from shipping_payment_area WHERE idshipping_regency ='$idReg'");
				while($row = mysql_fetch_array($subQuery)){
					$subArray[] = $row["sub_district"];
				}
			}
			mysql_close($con);
			return $subArray;
		}
		
		function getProductDetail($id){
			$con = openDatabaseConnection();
			
			$viewQuery = mysql_query("SELECT * from product WHERE idproduct ='$id'");
			$viewArray = array();
			
			while($viewArray[] = mysql_fetch_assoc($viewQuery)){}
			
			$jsonProduct = json_encode($viewArray);
			mysql_close($con);
			return $jsonProduct;
		}
		
		function viewProduct($firstIndex,$lastIndex){
			$con = openDatabaseConnection();
			
			$firstIndex = (int)$firstIndex;
			$lastIndex = (int)$lastIndex;
			$viewQuery = "";
			
			if($firstIndex <= 0){
				if($firstIndex == -4){
					$firstIndex = 0;
					$viewQuery = mysql_query("SELECT * from product LIMIT $firstIndex,1");
				}
				else if($firstIndex == -3){
					$firstIndex = 0;
					$viewQuery = mysql_query("SELECT * from product LIMIT $firstIndex,2");
				}
				else if($firstIndex == -2){
					$firstIndex = 0;
					$viewQuery = mysql_query("SELECT * from product LIMIT $firstIndex,3");
				}
				else if($firstIndex == -1){
					$firstIndex = 0;
					$viewQuery = mysql_query("SELECT * from product LIMIT $firstIndex,4");
				}
				else if($firstIndex == 0){
					$firstIndex = 0;
					$viewQuery = mysql_query("SELECT * from product LIMIT $firstIndex,5");
				}
			}
			else{
				$viewQuery = mysql_query("SELECT * from product LIMIT $firstIndex,$lastIndex");
			}

			$viewArray = array();
			
			while($viewArray[] = mysql_fetch_assoc($viewQuery)){
				//put all value inside array
			}
			
			$jsonProduct = json_encode($viewArray);
			
			mysql_close($con);
			return $jsonProduct;
		}
		
		function addProduct($productName, $productDetail, $productPrice, $productStock, $productTitle){
			$con = openDatabaseConnection();
			$productPrice = (double)$productPrice;
			$productStock = (int)$productStock;
			
			$productQuery = mysql_query("SELECT * from product WHERE product_name ='$productName'");
			$productRow = mysql_num_rows($productQuery);
			
			if($productRow == 0){ //product has not yet existed
				mysql_query("INSERT INTO product(product_name, product_details, product_price, product_stock, product_photo_title) VALUES ('$productName','$productDetail','$productPrice','$productStock','$productTitle')");
			}
			else{
				
			}
			mysql_close($con);	
			return $productRow; 
		}
		
		function removeProduct($id){
			$con = openDatabaseConnection();
			$nameQuery = mysql_query("SELECT product_name FROM product WHERE idproduct = '$id'");
			$name = "";
			
			while($row = mysql_fetch_array($nameQuery)){
				$name = $row["product_name"]; //get product name to be used as a message for removal
			}
			
			//remove the data
			mysql_query("DELETE from product WHERE idproduct = '$id'");
			mysql_close($con);	
			return $name;
		}
		
		function updateProduct($productName, $productDetail, $productPrice, $productStock, $image, $id){
			$con = openDatabaseConnection();
			$markUpdate = 0; //if 0, then cannot update!
			$productPrice = (double)$productPrice;
			$productStock = (int)$productStock;
			
			$productQuery = mysql_query("SELECT * from product WHERE product_name ='$productName'");
			$productRow = mysql_num_rows($productQuery);
			
			if($image == ""){
				if($productRow == 0){ //the name has not yet been used, and thus you can update it! SUCCEED
					//images are not updated, only data
					$markUpdate = 1;
					mysql_query("UPDATE product set product_name = '$productName', product_details = '$productDetail', product_price ='$productPrice', product_stock = '$productStock' WHERE idproduct = '$id'");
				}
				else{
					//check first thd id product
					$idProd = 0;
					while($row = mysql_fetch_array($productQuery)){
						$idProd = $row["idproduct"];
					}
					
					if($idProd == $id){ //SUCCEED
						$markUpdate = 1; //can update because it refers to the same row, if the name does not change it is okay
						mysql_query("UPDATE product set product_name = '$productName', product_details = '$productDetail', product_price ='$productPrice', product_stock = '$productStock' WHERE idproduct = '$id'");
					}
					else if($idProd != $id){ //SUCCEED
						$markUpdate = 0;
					}
				}
			}
			else{
				if($productRow == 0){ //the name has not yet been used, and thus you can update it! SUCCEED
					$title = "Product_Photo/".$image;
					unlink($title);//remove the picture first
					//the new image will be stored later in the hummel_controller.php
					
					//DATA update start from here
					$markUpdate = 1;
					mysql_query("UPDATE product set product_name = '$productName', product_details = '$productDetail', product_price ='$productPrice', product_stock = '$productStock', product_photo_title = '$image' WHERE idproduct = '$id'");
				}
				else{
					//check first thd id product
					$idProd = 0;
					while($row = mysql_fetch_array($productQuery)){
						$idProd = $row["idproduct"];
					}
					
					if($idProd == $id){ //SUCCEED
						$title = "Product_Photo/".$image;
						unlink($title);//remove the picture first
						//the new image will be stored later in the hummel_controller.php
						
						//DATA update start from here
						$markUpdate = 1;
						mysql_query("UPDATE product set product_name = '$productName', product_details = '$productDetail', product_price ='$productPrice', product_stock = '$productStock', product_photo_title = '$image' WHERE idproduct = '$id'");
					}
					else if($idProd != $id){ //SUCCEED
						$markUpdate = 0;
					}
				}
			}
				
			mysql_close($con);
			return $markUpdate;
		}
		
		function isEmailUnique($email){
			$con = openDatabaseConnection();
			$unique = false;
			
			$emailQuery = mysql_query("SELECT * from user WHERE email = '$email'");
			$emailRow = mysql_num_rows($emailQuery);
			
			if($emailRow == 0){
				//email has never been used before
				$unique = true;
			}
			else{
				//email has been used
			}
			mysql_close($con);
			return $unique;
		}
		
		function regisUser($firstName,$lastName,$email,$pass,$address,$postalCode,$phone,$regency,$subDistrict){
			$con = openDatabaseConnection();
			mysql_query("INSERT INTO user(first_name,last_name,email,passsword,address,postal_code,phone_number,regency,sub_district) VALUES ('$firstName','$lastName','$email','$pass','$address','$postalCode','$phone','$regency','$subDistrict')");
			mysql_close($con);
		}
		
		function loginUser($email,$pass){
			$con = openDatabaseConnection();
			$loginQuery = mysql_query("SELECT * from user WHERE email = '$email' AND passsword = '$pass'");
			$loginArray = array();
			
			while($loginArray[] = mysql_fetch_assoc($loginQuery)){}
			
			$jsonLogin = json_encode($loginArray);
			mysql_close($con);
			return $jsonLogin;
		}
		
		function checkProdNumber($id){
			$con = openDatabaseConnection();
			$checkQuery = mysql_query("SELECT * from product WHERE idproduct = '$id'");
			$prodNumber = 0;
			
			while($row = mysql_fetch_array($checkQuery)){
				$prodNumber = $row["product_stock"];
			}
			mysql_close($con);
			return $prodNumber;
		}
		
		function addToCart($cartName,$id,$totalBuy,$totalPrice,$idUser){
			$con = openDatabaseConnection();
			
			//check whether the cart name has been available or not, if yes, do not create the new one
			$checkQuery = mysql_query("SELECT * from shopping_cart WHERE shopping_cart_name ='$cartName'");
			$checkRow = mysql_num_rows($checkQuery);
			
			if($checkRow == 0){
				//CODE BELOW: CREATE NEW SHOPPING CART
				mysql_query("INSERT INTO shopping_cart (shopping_cart_name, iduser) VALUES ('$cartName','$idUser')");
			}
			else{} // Do not create new shopping cart
			
			//Find the id of the corresponding cart
			$idQuery = mysql_query("SELECT * from shopping_cart WHERE shopping_cart_name = '$cartName'");
			$idCart = 0;
			
			while($row = mysql_fetch_array($idQuery)){
				$idCart = $row["idshopping_cart"]; //retrieve the id cart of course
			}
			//============================================================================================
			//input the contents to the table shopping_cart_detail
			
			//1. check first whether the corresponding item has been added to the table shopping cart detail or not, if yes, just update it, if not, insert new line
			
			$checkItemQuery = mysql_query("SELECT * from shopping_cart_detail WHERE id_item ='$id' AND idshopping_cart = '$idCart'");
			$checkItemRow = mysql_num_rows($checkItemQuery);
			
			if($checkItemRow == 0){ //The item has not yet been added to the cart, thus insert new one
				mysql_query("INSERT INTO shopping_cart_detail (idshopping_cart,id_item,total_bought,total_price) VALUES ('$idCart','$id','$totalBuy','$totalPrice')");	
			}
			else{ //simply update the item bought in the table shopping cart detail
				$rowID = 0; //the row ID where the discussed item is put in the take
				$totalPrevious = 0;
				$pricePrevious = 0.0;
				while($row = mysql_fetch_array($checkItemQuery)){
					$rowID = $row["idshopping_cart_detail"];
					$totalPrevious = $row["total_bought"];
					$pricePrevious = $row["total_price"];
				}
				
				$priceOriginal = $pricePrevious / $totalPrevious;
				$totalNew = $totalPrevious + $totalBuy;
				$priceNew = $priceOriginal * $totalNew;
				//update the contents, but how will be the price
				mysql_query("UPDATE shopping_cart_detail SET total_bought = '$totalNew', total_price = '$priceNew' WHERE idshopping_cart_detail = '$rowID'");
			}			

			//=============================================================================================
			//status code below: WORKING PROPERLY
			//get the current stock from the table product
			$prodQuery = mysql_query("SELECT * from product WHERE idproduct = '$id'");
			$prodStock = 0;
			
			while($row = mysql_fetch_array($prodQuery)){
				$prodStock = $row["product_stock"];
			}
			//reduce the stock in the table product
			$stockLeft = $prodStock - $totalBuy;
			//updating the new stock in the table product
			mysql_query("UPDATE product SET product_stock ='$stockLeft' WHERE idproduct = '$id'") or die(mysql_error());
			mysql_close($con);
		}
		
		function viewCart($cartName){
			$con = openDatabaseConnection();
			$idQuery = mysql_query("SELECT * from shopping_cart WHERE shopping_cart_name ='$cartName'");
			$idCart = 0;
			
			//get the id of the corresponding cart
			while($row = mysql_fetch_array($idQuery)){
				$idCart = $row["idshopping_cart"];
			}
			
			$cartQuery = mysql_query("SELECT * from shopping_cart_detail WHERE idshopping_cart ='$idCart'");
			$cartArray = array();
			
			while($cartArray[] = mysql_fetch_assoc($cartQuery)){}
			
			$jsonCart = json_encode($cartArray);
			mysql_close($con);
			return $jsonCart;
		}
		
		function updateCart($idItem,$totalNew,$totalUpdate,$cartName){
			$con = openDatabaseConnection();
			//find cart id
			$idQuery = mysql_query("SELECT * from shopping_cart WHERE shopping_cart_name = '$cartName'") or die(mysql_error());
			$idCart = 0;
			
			while($row = mysql_fetch_array($idQuery)){
				$idCart = $row["idshopping_cart"];
			}
			
			//updating items in table product
			mysql_query("UPDATE product SET product_stock ='$totalNew' WHERE idproduct ='$idItem'") or die(mysql_error());
			
			//finding price of the corresponding item
			$priceQuery = mysql_query("SELECT * from product WHERE idproduct = '$idItem'");
			$price = 0.0;
			
			while($row = mysql_fetch_array($priceQuery)){
				$price = $row["product_price"];
			}
			
			$totalPrice = $price * $totalUpdate;
			
			//updating items in table shopping_cart_detail
			mysql_query("UPDATE shopping_cart_detail SET total_bought = '$totalUpdate', total_price ='$totalPrice' WHERE id_item = '$idItem' AND idshopping_cart = '$idCart'") or die(mysql_error());
			mysql_close($con);
		}
		
		function removeCart($idItem,$totalStock,$cartName){
			$con = openDatabaseConnection();
			$idQuery = mysql_query("SELECT * from shopping_cart WHERE shopping_cart_name ='$cartName'");
			$idCart = 0;
			
			//get the id of the corresponding cart
			while($row = mysql_fetch_array($idQuery)){
				$idCart = $row["idshopping_cart"];
			}
			
			//remove the items from the shopping cart
			mysql_query("DELETE FROM shopping_cart_detail WHERE id_item = '$idItem' AND idshopping_cart ='$idCart'");
			
			//take the last stock in table product
			$stockQuery = mysql_query("SELECT * from product WHERE idproduct ='$idItem'");
			$stock = 0;
			
			while($row = mysql_fetch_array($stockQuery)){
				$stock = $row["product_stock"];
			}
			
			//we add it because when we remove from cart, it means we add to warehouse again
			$stock = $stock + $totalStock;
			
			//now we will update the content in table product
			mysql_query("UPDATE product SET product_stock ='$stock' WHERE idproduct ='$idItem'");	
			mysql_close($con);
		}
		
		function checkAvailCart($idUser){
			$con = openDatabaseConnection();
			$cartQuery = mysql_query("SELECT * from shopping_cart WHERE iduser ='$idUser'") or die(mysql_error());
			$cartDataArray = array(); //contain the data in table shopping cart
			while($cartDataArray[] = mysql_fetch_assoc($cartQuery)){}
			mysql_close($con);
			return $cartDataArray;
		}
		
		function matchCartWithConfirm($idCart){
			//this function is used to match whether an id cart has been stored in table confirmation or not
			//if yes, then the cart must never be accessed again (bool false). If no, it can still be accessed (bool true)
			$con = openDatabaseConnection();
			$match = false; 
			$matchQuery = mysql_query("SELECT * from confirmation_table WHERE idshopping_cart ='$idCart'");
			$matchRow = mysql_num_rows($matchQuery);
			
			if($matchRow == 1){ //id cart has existed and thus it must never be accessed again
				$match = false;
			}
			else if($matchRow == 0){ //the cart can still be accessed
				$match = true;
			}
			
			mysql_close($con);
			return $match;
		}
		
		function deleteCart($idCart,$cartName,$idUser){
			//this function is not for removing items inside the cart, but to destroy the cart itself
			$con = openDatabaseConnection();
			$cartQuery = mysql_query("SELECT * from shopping_cart WHERE shopping_cart_name ='$cartName'") or die(mysql_error());
			$idCurrent = 0;
			$markRemove = 0;
			while($row = mysql_fetch_array($cartQuery)){
				$idCurrent = $row["idshopping_cart"];
			}

			if($idCurrent == $idCart){
				//====remove the current loaded shopping cart==================
				$markRemove = 1;
				//retrieve the items first
				$itemQuery = mysql_query("SELECT * from shopping_cart_detail WHERE idshopping_cart ='$idCart'");
				$idItem = 0;
				$itemCart = 0;
				while($row = mysql_fetch_array($itemQuery)){
					//get the total item from the cart
					$itemCart = $row["total_bought"];
					$idItem = $row["id_item"];
					
					$stockQuery = mysql_query("SELECT * from product WHERE idproduct ='$idItem'");
					$stock = 0;
					
					while($rows = mysql_fetch_array($stockQuery)){
						$stock = $rows["product_stock"];
					}
					
					$totalItem = $stock + $itemCart;
					
					//update the total in the table product
					mysql_query("UPDATE product SET product_stock ='$totalItem' WHERE idproduct ='$idItem'");
				}
				
				//just delete the items
				//mysql_query("DELETE FROM shopping_cart_detail WHERE idshopping_cart ='$idCart'");
				mysql_query("DELETE FROM shopping_cart WHERE idshopping_cart ='$idCart'");
			}
			else if($idCurrent != $idCart){
				//====remove the unloaded cart========================
				$markRemove = 0;
				//retrieve the items first
				$itemQuery = mysql_query("SELECT * from shopping_cart_detail WHERE idshopping_cart ='$idCart'");
				$idItem = 0;
				$itemCart = 0;
				while($row = mysql_fetch_array($itemQuery)){
					//get the total item from the cart
					$itemCart = $row["total_bought"];
					$idItem = $row["id_item"];
					
					$stockQuery = mysql_query("SELECT * from product WHERE idproduct ='$idItem'");
					$stock = 0;
					
					while($rows = mysql_fetch_array($stockQuery)){
						$stock = $rows["product_stock"];
					}
					
					$totalItem = $stock + $itemCart;
					
					//update the total in the table product
					mysql_query("UPDATE product SET product_stock ='$totalItem' WHERE idproduct ='$idItem'");
				}
				
				//delete the shopping cart
				mysql_query("DELETE FROM shopping_cart WHERE idshopping_cart ='$idCart'");
			}
			mysql_close($con);
			return $markRemove;
		}
		
		function clearCart($cartName){
			$con = openDatabaseConnection();
			$idQuery = mysql_query("SELECT * from shopping_cart WHERE shopping_cart_name ='$cartName'");
			$idCart = 0;
			
			//get the id of the corresponding cart
			while($row = mysql_fetch_array($idQuery)){
				$idCart = $row["idshopping_cart"];
			}
			
			//retrieve items from cart
			$shopQuery = mysql_query("SELECT * from shopping_cart_detail WHERE idshopping_cart ='$idCart'");
			$shopArray = array();
			
			while($shopArray[] = mysql_fetch_assoc($shopQuery)){} 
			
			//$jsonShop = json_encode($shopArray); //not necessary
			
			//update the items to the table product
			foreach($shopArray as $shop){
				$idItem = $shop["id_item"];
				$totalBought = $shop["total_bought"];
				
				//take the last stock in table product
				$stockQuery = mysql_query("SELECT * from product WHERE idproduct ='$idItem'");
				$stock = 0;
				
				while($row = mysql_fetch_array($stockQuery)){
					$stock = $row["product_stock"];
				}
				
				$stock = $stock + $totalBought;
				
				//update the items in table product
				mysql_query("UPDATE product SET product_stock ='$stock' WHERE idproduct='$idItem'");
			}
			
			//after the update to table product process has been done, now it is turn to remove the items in the corresponding cart
			mysql_query("DELETE from shopping_cart_detail WHERE idshopping_cart = '$idCart'");
			mysql_close($con);
		}
		
		function getItemName($idItem){
			$con = openDatabaseConnection();
			$nameQuery = mysql_query("SELECT * from product WHERE idproduct = '$idItem'");
			$name = "";
			
			while($row = mysql_fetch_array($nameQuery)){
				$name = $row["product_name"];
			}
			mysql_close($con);
			return $name;
		}
		
		function getCartDetail($cartName){
			//this is to check whether the cart is currently having items or not
			$con = openDatabaseConnection();
			$idQuery = mysql_query("SELECT * from shopping_cart WHERE shopping_cart_name ='$cartName'");
			$idCart = 0;
			
			//get the id of the corresponding cart
			while($row = mysql_fetch_array($idQuery)){
				$idCart = $row["idshopping_cart"];
			}
			
			//retrieve items from cart
			$shopQuery = mysql_query("SELECT * from shopping_cart_detail WHERE idshopping_cart ='$idCart'");
			$totalRow = mysql_num_rows($shopQuery);
			
			mysql_close($con);
			return $totalRow;
		}
		
		function getShipPrice($regency,$subDistrict,$delService){
			$con = openDatabaseConnection();
			$priceShip = 0.0;
			//find the id regency first
			$regencyQuery = mysql_query("SELECT * from shipping_regency WHERE regency ='$regency'"); 
			$idReg = 0;
				
			while($row = mysql_fetch_array($regencyQuery)){
				$idReg = $row["idshipping_regency"];
			}
			
			//get the price of the shipping
			$shipQuery = mysql_query("SELECT * from shipping_payment_area WHERE idshipping_regency = '$idReg' AND sub_district ='$subDistrict'");
			
			if($delService == "Package Reguler"){
				while($row = mysql_fetch_array($shipQuery)){
					$priceShip = $row["price_shipping_reguler"];
				}
			}
			else if($delService == "Package OK"){
				while($row = mysql_fetch_array($shipQuery)){
					$priceShip = $row["price_shipping_ok"];
				}
			}
			mysql_close($con);
			return $priceShip;
		}
		
		function addShippingData($address,$postalCode,$phoneNumber,$regency,$subDistrict,$confirmationCode){ //WORKING
			$con = openDatabaseConnection();
			
			//check first if the confirmation code has existed or not
			$confirmQuery = mysql_query("SELECT * from shipping_destination WHERE confirmation_code ='$confirmationCode'");
			$confirmRow = mysql_num_rows($confirmQuery);
			
			if($confirmRow == 0){ //confirm code has not yet existed
				mysql_query("INSERT INTO shipping_destination(address,postal_code,phone_number,regency,sub_district,confirmation_code) VALUES ('$address','$postalCode','$phoneNumber','$regency','$subDistrict','$confirmationCode')") or die (mysql_error());
			}
			else{
				//do nothing because it has existed!
			}
			mysql_close($con);
		}
		
		function addConfirmationData($cartName,$idUser,$allPrice,$shipCost,$totalPaid,$confirmationCode){
			$con = openDatabaseConnection();
			$idQuery = mysql_query("SELECT * from shopping_cart WHERE shopping_cart_name ='$cartName'");
			$idCart = 0;
			
			//get the id of the corresponding cart
			while($row = mysql_fetch_array($idQuery)){
				$idCart = $row["idshopping_cart"];
			}
			
			//check first if the confirmation code has existed or not
			$confirmQuery = mysql_query("SELECT * from confirmation_table WHERE confirmation_code ='$confirmationCode'");
			$confirmRow = mysql_num_rows($confirmQuery);
			
			if($confirmRow == 0){ //confirm code has not yet existed
				mysql_query("INSERT INTO confirmation_table(idshopping_cart,id_user,total_price_items,shipping_cost,total_price_paid,confirmation_code) VALUES ('$idCart','$idUser','$allPrice','$shipCost','$totalPaid','$confirmationCode')") or die (mysql_error());
			}
			else{
				//do nothing because it has existed!
			}
			mysql_close($con);
		}
		
		function checkConfirmationStatus($confirmCode){
			$con = openDatabaseConnection();
			$confirmQuery = mysql_query("SELECT * from confirmation_table WHERE confirmation_code ='$confirmCode'") or die(mysql_error());
			//echo "SELECT * from confirmation_table WHERE confirmation_code ='$confirmCode'<br>";
			//echo "rows is ".mysql_num_rows($confirmQuery);
			$status = "";
			
			while($row = mysql_fetch_array($confirmQuery)){
				$status = $row["paymet_status"];
			}
			
			mysql_close($con);
			return $status;
		}
		
		function checkConfirmationItem($confirmCode){
			$con = openDatabaseConnection();
			$confirmQuery = mysql_query("SELECT * from confirmation_table WHERE confirmation_code ='$confirmCode'");
			$idCart = 0;
			
			while($row = mysql_fetch_array($confirmQuery)){
				$idCart = $row["idshopping_cart"];
			}
			
			$itemQuery = mysql_query("SELECT * from shopping_cart_detail WHERE idshopping_cart ='$idCart'");
			$itemArray = array();
			
			while($itemArray[] = mysql_fetch_assoc($itemQuery)){}
			
			mysql_close($con);
			return $itemArray;
		}
		
		function checkConfirmationAddress($confirmCode){
			$con = openDatabaseConnection();
			$confirmQuery = mysql_query("SELECT * from shipping_destination WHERE confirmation_code ='$confirmCode'");
			
			$addressArray = array();
			while($addressArray[] = mysql_fetch_assoc($confirmQuery)){}
			
			mysql_close($con);
			return $addressArray;
		}
		
		function sendConfirmMessage($confirmCode,$confirmMessage){
			$con = openDatabaseConnection();
			mysql_query("INSERT INTO confirmation_message(message,confirmation_code) VALUES ('$confirmMessage','$confirmCode')");
			mysql_close($con);
		}
		
		function getMessageRows(){
			$con = openDatabaseConnection();
			$rowQuery = mysql_query("SELECT * from confirmation_message");
			$rowNumber = mysql_num_rows($rowQuery);
			mysql_close($con);
			return $rowNumber;
		}
		
		function viewMessages($firstMessage,$totalView){
			/* $con = openDatabaseConnection();
			$messageQuery = mysql_query("SELECT * from confirmation_message LIMIT $firstMessage, $totalView");
			$messageArray = array();
			
			while($messageArray[] = mysql_fetch_assoc($messageQuery)){}
			mysql_close($con);
			return $messageArray; */
			$con = openDatabaseConnection();
			
			//echo "first is ".$firstMessage."<br>";
			//echo "total is ".$totalView."<br>";
			
			$firstMessage = (int)$firstMessage;
			$totalView = (int)$totalView;
			$viewQuery = "";
			
			if($firstMessage <= 0){
				if($firstMessage == -4){
					$firstMessage = 0;
					$viewQuery = mysql_query("SELECT * from confirmation_message LIMIT $firstMessage,1");
				}
				else if($firstMessage == -3){
					$firstMessage = 0;
					$viewQuery = mysql_query("SELECT * from confirmation_message LIMIT $firstMessage,2");
				}
				else if($firstMessage == -2){
					$firstMessage = 0;
					$viewQuery = mysql_query("SELECT * from confirmation_message LIMIT $firstMessage,3");
				}
				else if($firstMessage == -1){
					$firstMessage = 0;
					$viewQuery = mysql_query("SELECT * from confirmation_message LIMIT $firstMessage,4");
				}
				else if($firstMessage == 0){
					$firstMessage = 0;
					$viewQuery = mysql_query("SELECT * from confirmation_message LIMIT $firstMessage,5");
				}
			}
			else{
				//echo "ENTER HERE ECHO<br>";
				$viewQuery = mysql_query("SELECT * from confirmation_message LIMIT $firstMessage,$totalView");
			}

			$viewArray = array();
			
			while($viewArray[] = mysql_fetch_assoc($viewQuery)){
				//put all value inside array
			}
			//echo "rows ".mysql_num_rows($viewQuery)."<br>";
			//echo "size array is ".sizeof($viewArray);
			mysql_close($con);
			return $viewArray;
		}
		
		function getMessageDetail($id){
			$con = openDatabaseConnection();
			
			$viewQuery = mysql_query("SELECT * from confirmation_message WHERE idconfirmation_message ='$id'");
			
			//update the status message then
			$status = "Read";
			mysql_query("UPDATE confirmation_message SET status_message = '$status' WHERE idconfirmation_message = '$id'");
			
			$viewArray = array();
			
			while($viewArray[] = mysql_fetch_assoc($viewQuery)){}

			mysql_close($con);
			return $viewArray;
		}
		
		function getTheUser($idCart){
			$con = openDatabaseConnection();
			$cartQuery = mysql_query("SELECT * from shopping_cart WHERE idshopping_cart ='$idCart'");
			$idUser = 0;
			
			while($row = mysql_fetch_array($cartQuery)){
				$idUser = $row["iduser"];
			}
			
			$userQuery = mysql_query("SELECT * from user WHERE iduser ='$idUser'");
			
			$userArray = array();
			
			while($userArray[] = mysql_fetch_assoc($userQuery)){}
			mysql_close($con);
			return $userArray;
		}
		
		function updatePaymentStatus($confirmCode){
			$con = openDatabaseConnection();
			$paymentStats = "Already Paid. Waiting for shipping code";
			mysql_query("UPDATE confirmation_table SET paymet_status = '$paymentStats' WHERE confirmation_code = '$confirmCode'");
			mysql_close($con);
		}
		
		function check3DaysCart(){
			$con = openDatabaseConnection();
			$cartQuery = mysql_query("SELECT * from shopping_cart_detail");
			
			$date = "";
			$day = ""; //contains the Y-M-D
			$time = ""; //contain the time
			$markDay = 0; //if 0, still takes day, if 1 starts take time
			
			while($row = mysql_fetch_array($cartQuery)){
				$date = $row["date"];
				
				//loop the string date
				//echo "ori date is <b>".$date."</b><br>";
				for($i = 0; $i < strlen($date); $i++){
					if($date[$i] != " "){
						if($markDay == 0){
							$day .= $date[$i];
						}
						else if($markDay == 1){
							$time .= $date[$i];
						}
					}
					else if($date[$i] == " "){
						$markDay = 1;
						continue;
					}
				}
				
				$markDay = 0;
				//time will always be the same, but the date is the different one
				//loop the day
				$markSecond = 0;
				$dateNumber = "";
				for($i = 0; $i < strlen($day); $i++){
					if($day[$i] == "-"){
						$markSecond++;
					}
					else if($day[$i] != "-"){
						if($markSecond == 1){ //first strip sign, do not do anything
						}
						else if($markSecond == 2){ //second strip sign, do something of course
							$dateNumber .= $day[$i];
						}
					}
				}
				
				//loop date number to know whether the date number consists of 0 or not
				for($i = 0; $i < strlen($dateNumber); $i++){
					if($dateNumber[$i] == "0"){ //loop zero for the first time
						$temp = "";
						$temp .= $dateNumber[$i+1];
						$dateNumber = $temp;
						break;
					}
				}
				
				$dateNumber = (int) $dateNumber; //from 2013-04-16, takes the 16 only
				$dateNumbers = $dateNumber + 3; //indicate three days already 16+3 = 19
				
				//take the current date
				$currentDate = date("Y-m-d");
				$tempCurDate = $currentDate; //this is used later in the conditional if(currentDate != days)
				$currentDate = date("Y-m-d h:m:s");
				
				//replacing date number with date numbers
				$dateNumber = (string)$dateNumber;
				$dateNumbers = (string)$dateNumbers;
				$days = str_replace($dateNumber,$dateNumbers,$day);
				$tempDays = $days; //this is used later in the conditional if(currentDate != days)
				$days = $days." ".$time;
				
				//experiment first
				if($days == $currentDate){ //WORKING PROPERLY ALREADY ================================			
					//indicates three days already
					$idItem = $row["id_item"];
					$totalBuy = $row["total_bought"];
					$idCart = $row["idshopping_cart"];
					
					//by using the id cart, find first the shopping cart that has not yet been unconfirmed, but has exceeded three days
					// because if it is confirmed, then the function to remove the items shall be different (although it is still the same -- 3 days)
					$uncomQuery = mysql_query("SELECT * from confirmation_table WHERE idshopping_cart ='$idCart'");
					$uncomRow = mysql_num_rows($uncomQuery);
					
					//THE CONDITIONAL BELOW WORKS PROPERLY ALREADY============================
					if($uncomRow == 0){ //it means the corresponding cart indeed has not yet been confirmed its transaction (has not set shipping address, etc etc)
						//the if unconfirmrow = 0 works prperly already, this system deletes only the unconfirmed cart. like cart 77,78,79, only 79 is confirmed, and 77 and 78 diesss
						
						//remove the items from the shopping cart
						storeItemFromCart($idCart,$totalBuy,$idItem); //WORKING PROPERLY, IN CASE IT IS NOT, JUST COPY PASTE THE CODE INSIDE THE FUNCTION HERE
						
						//echo "I ENTER THIS CONDITIONAL LOHHH<br>"; //check berikutnya TANGGAL 22 ========================
					}
					else{ //the cart has been confirmed, basically it must be removed too, but guess it is better to separate the function to not mess things up
					
					}
				}
				else if($days != $currentDate){
					//this can be the days and the currentd ate is exactly not same
					//but it can also the same but different time
					//example: 2013-03-03 08:08:09 with 2013-03-03 09:09:10 same date different time
					//so to overcome this matter, we should add additional conditional
					
					//indicates three days already
					$idItem = $row["id_item"];
					$totalBuy = $row["total_bought"];
					$idCart = $row["idshopping_cart"];
					
					//by using the id cart, find first the shopping cart that has not yet been unconfirmed, but has exceeded three days
					// because if it is confirmed, then the function to remove the items shall be different (although it is still the same -- 3 days)
					$uncomQuery = mysql_query("SELECT * from confirmation_table WHERE idshopping_cart ='$idCart'");
					$uncomRow = mysql_num_rows($uncomQuery);
					
					$hour = "";
					$minute = "";
					$second = "";
					
					if($uncomRow == 0){ //it means the corresponding cart indeed has not yet een confirmed its transaction (has not set shipping address, etc etc)
						//the if unconfirmrow = 0 works prperly already, this system deletes only the unconfirmed cart. like cart 77,78,79, only 79 is confirmed, and 77 and 78 diesss
						$idItem = $row["id_item"];
						$totalBuy = $row["total_bought"];
						$idCart = $row["idshopping_cart"];
						if($tempDays == $tempCurDate){
							//echo "ENTERING TEMP DAYS EQUALS TO TEMP CURRENT DATE TROLOLO<br><br>";
							//check the time
							//loop the time first and takes hour, minute and second from the stored time (stored in db)
							$markTime = 0; //if 0, still takes hour, if 1 takes minutes, if 2 takes second
							for($i = 0; $i < strlen($time); $i++){
								if($time[$i] == ":"){
									$markTime++;
								}
								else if($time[$i] != ":"){
									if($markTime == 0){
										//take hour
										$hour .= $time[$i];
									}
									else if($markTime == 1){
										//take minute
										$minute .= $time[$i];
									}
									else if($markTime == 2){
										//take second
										$second .= $time[$i];
									}
								}
							}
							
							//takes time from current date
							$currentHour = date("H");
							$currentMinute = date("i");
							$currentSecond = date("s");
							
							//compare hour first
							if($currentHour >= $hour){
								//echo "current hour is greater<br>";
								if($currentHour > $hour){
									//instant delete
									storeItemFromCart($idCart,$totalBuy,$idItem);
								}
								else if($currentHour == $hour){
									//selection must be done
									//now check the minute first
									if($currentMinute > $minute){
										//example: minute current is 15 but minute in db is 14, means INSTANT DELETE --> 18.15 in current, 18.14 in db
										storeItemFromCart($idCart,$totalBuy,$idItem);
									}
									else if($currentMinute == $minute){
										//says 18:15:23 and 18:15:58 --> it is just different several seconds, not that meaningful
										//simply remove it then
										storeItemFromCart($idCart,$totalBuy,$idItem);
									} 
									//storeItemFromCart($idCart,$totalBuy,$idItem); //TESTING PURPOSE, CAN OR CANNOT DETECT HOUR
								}
							}
							else if($currentHour < $hour){
								//means date is same, time is different but hour is lower like current hour is 17.00 but the hour in database in 18.00
								//this condition makes the system does not have to work at all
							}
							
							//echo "current hour is <b>".$currentHour."</b> current minute is <b>".$currentMinute."</b> current second is <b>".$currentSecond."</b><br>";
							//echo "hour is <b>".$hour."</b> minute is <b>".$minute."</b> second is <b>".$second."</b><br>";
						}
					}
					//echo "I ENTER SECOND CONTIONAL LOHHH<br>";
				}
				
				/* echo "current date is <b>".$currentDate."</b><br>";
				echo "days is <b>".$days."</b><br><br>";
				echo "Date is ".$day." Time is ".$time." Replaced date is ".$days."<br>";
				echo "current date is ".$currentDate."<br>";
				echo "date number is ".$dateNumber." date numbers is ".$dateNumbers."<br><br><hr>"; */
				$day = "";
				$time = "";
				$dateNumber = "";
				$dateNumbers = 0;
			}
			mysql_close($con);
		}
		
		function check3DaysConfirm(){
			$con = openDatabaseConnection();
			$comQuery = mysql_query("SELECT * from confirmation_table");
			$status = "";
			
			while($row = mysql_fetch_array($comQuery)){
				$status = $row["paymet_status"];
				$confirmCode = $row["confirmation_code"];
				$idCart = $row["idshopping_cart"];
				$time = "";
				$markTime = 0;
				
				if($status == "Waiting for payment"){
					//take the time
					for($i = 0; $i < strlen($confirmCode); $i++){
						if($confirmCode[$i] == '-' && $confirmCode[$i+1] == '-'){ //-
							//==TWO CONDITIONAL BELOWS IS USED TO SKIP TWO INDEXES OF THE STRING, WHICH IS THE I AND I+1 WHICH IS THE - AND THE -
							if($markTime == 0){
								$markTime++;
								continue;
							}
						}
						else{
							if($markTime == 1){
								$markTime++;
								continue;
							} //skip one more index
							else if($markTime == 2){
								//starts working
								if($confirmCode[$i] != '-'){
									$time .= $confirmCode[$i];
								}
								else if($confirmCode[$i] == '-'){
									break; //break looping
								}
							}
						}
					}
					//retrieval of time has been correct ALREADY!!
					
					$currentTime = time();
					$minus = $currentTime - $time; //difference between current time and the time stored in db
					$threeDaysTime = 3 * 24 * 60 * 60;
					
					//echo "time is <b>".$time."</b> current time is <b>".$currentTime."</b><br>";
					//echo "Minus is <b>".$minus."</b><br><br>";
					//echo "Three days time is <b>".$threeDaysTime."</b><br><br>";
					
					if($minus >= $threeDaysTime){
						//remove confirmed cart
						$cartQuery = mysql_query("SELECT * from shopping_cart_detail WHERE idshopping_cart ='$idCart'");
						$idItem = 0;
						$totalBuy = 0;
						
						while($rows = mysql_fetch_array($cartQuery)){
							$idItem = $rows["id_item"];
							$totalBuy = $rows["total_bought"];
						}
						storeItemFromCart($idCart,$totalBuy,$idItem);
						
						//update status payment
						$statusCancel = "Transaction has been canceled (exceed 3 days)";
						mysql_query("UPDATE confirmation_table SET paymet_status ='$statusCancel' WHERE confirmation_code ='$confirmCode'");
					}
					else if($minus < $threeDaysTime){
						//do not remove since it has not yet been three days
					}
				}
			}
			
			mysql_close($con);
		}
		
		function storeItemFromCart($idCart,$totalBuy,$idItem){
			//remove the items from the shopping cart
			mysql_query("DELETE FROM shopping_cart_detail WHERE idshopping_cart = '$idCart' AND id_item = '$idItem'");
						
			$stockQuery = mysql_query("SELECT * from product WHERE idproduct = '$idItem'");
			$stock = 0;
						
			while($rows = mysql_fetch_assoc($stockQuery)){
				$stock = $rows["product_stock"];
			}
						
			$stock = $stock + $totalBuy;
						
			//update the items in the table product
			mysql_query("UPDATE product SET product_stock = '$stock' WHERE idproduct = '$idItem'");
		}
		
		function addShipping($confirmCode,$shippingCode){
			$con = openDatabaseConnection();
			//check whether shipping code has been inserted or not
			$shipQuery = mysql_query("SELECT * from checkout WHERE confirmation_code ='$confirmCode' AND shipping_code = '$shippingCode'") or die (mysql_error());
			$shipRows = mysql_num_rows($shipQuery);
			$status = "";
			
			if($shipRows == 0){ //data has not yet existed
				//insert shipping code
				mysql_query("INSERT INTO checkout (confirmation_code, shipping_code) VALUES ('$confirmCode','$shippingCode')") or die (mysql_error());
				
				//update status shipping
				$statShip = "Shipped Already";
				mysql_query("UPDATE confirmation_message SET status_shipping = '$statShip' WHERE confirmation_code ='$confirmCode'") or die (mysql_error());
				
				//update status payment
				$statPay = "Item has been shipped";
				mysql_query("UPDATE confirmation_table SET paymet_status = '$statPay' WHERE confirmation_code ='$confirmCode'") or die (mysql_error());
				$status = "Shipping Success";
			}
			else{ //data existed already
				$status = "Shipping Code Entered";
			}
			mysql_close($con);
			return $status;
		}
		
		function checkItemsShipped($shipCode){
			$con = openDatabaseConnection();
			$searchQuery = mysql_query("SELECT * from checkout WHERE shipping_code ='$shipCode'");
			$confirmCode = "";
			
			while($row = mysql_fetch_array($searchQuery)){
				$confirmCode = $row["confirmation_code"];
			}
			
			$cartQuery = mysql_query("SELECT * from confirmation_table WHERE confirmation_code ='$confirmCode'");
			$idCart = 0;
			
			while($row = mysql_fetch_array($cartQuery)){
				$idCart = $row["idshopping_cart"];
			}
			
			$itemQuery = mysql_query("SELECT * from shopping_cart_detail WHERE idshopping_cart ='$idCart'");
			$itemArray = array();
			
			while($itemArray[] = mysql_fetch_assoc($itemQuery)){}
			
			mysql_close($con);
			return $itemArray;
		}
?>