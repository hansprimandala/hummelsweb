<?php session_start();
require_once 'Excel/reader.php';
include("hummel_model.php");
	if(isset($_POST["input"])){
		$input = $_POST["input"];
		
		if($input == "loginAdmin"){
			if(isset($_POST["adminEmail"]) && isset($_POST["adminPass"])){
				$adminEmail = $_POST["adminEmail"];
				$adminPass = $_POST["adminPass"];
				
				if($adminEmail != "" && $adminPass != ""){
					//$contentPage = file_get_contents("http://localhost/hummelsweb/hummel_model.php");
					$message = loginAdmin($adminEmail,$adminPass);
					
					//echo $message;
					if($message == "error_log_in"){
						header("Location:admin_index.php?message=error_login");
					}
					else{
						//return json data
						$_SESSION["jsonLogin"] = $message;
						header("Location:admin_page.php");
					} 
				}
				else if($adminEmail == "" || $adminPass == ""){
					header("Location:admin_index.php?message=empty_fill");
				}
			}
		}
		else if($input == "uploadShip"){
			if(isset($_POST["shippingFile"])){
				$shippingFile = $_POST["shippingFile"];
				//echo "Shipping file name is ".$shippingFile; //successfully retrieve the file name indeed!!!
				
				// ExcelFile($filename, $encoding);
				$data = new Spreadsheet_Excel_Reader();
				// Set output Encoding.
				$data->setOutputEncoding('CP1251');
				$data->read($shippingFile);
				
				error_reporting(E_ALL ^ E_NOTICE);
				
				$regency = "";
				$subDistrict = "";
				$priceReg = "";
				$priceOK = "";
				
				for ($i = 2; $i <= $data->sheets[0]['numRows']; $i++) {
					/**/
					
					//echo "Regency :<b>".$regency."</b> Sub-District: <b>".$subDistrict."</b> Price Reguler<b> : ".$priceReg."</b> Price OK <b>".$priceOK."</b><br>"; //Succeed!!
					$jsonShip = "";
					if($data->sheets[0]['cells'][$i][1] == "" || $data->sheets[0]['cells'][$i][2] == "" || $data->sheets[0]['cells'][$i][3] == "" || $data->sheets[0]['cells'][$i][4] == ""){
						//echo "<br><br><I>CHANGING ROW</I><br><br>"; //success!!
						continue; //skip the index!
					}
					else{
						$regency = $data->sheets[0]['cells'][$i][1];
						$subDistrict = $data->sheets[0]['cells'][$i][2];
						$priceReg = $data->sheets[0]['cells'][$i][3];
						$priceOK = $data->sheets[0]['cells'][$i][4];
						
						storeShippingCost(addslashes($regency),addslashes($subDistrict),addslashes($priceReg),addslashes($priceOK));
					}
				}
				//echo "DONE";
				header("Location:admin_page.php?message=data_uploaded");
			}
			else{
				header("Location:admin_page.php?message=data_not_uploaded");
			}
		}
		else if($input == "removeShip"){
			removeShippingCost();
			header("Location:admin_page.php?message=data_removed");
		}
		else if($input == "viewShip"){
			if(isset($_POST["index"])){
				$index = $_POST["index"];
				$_SESSION["jsonRegency"] = viewRegency((int)$index,10);
				$_SESSION["jsonSubDis"] = viewSubdistrict((int)$index,10);
				header("Location:admin_page.php?message=show_reg_dis&index=$index");
			}
		}
		else if($input == "viewProduct"){
			if(isset($_POST["firstIndex"]) && isset($_POST["lastIndex"])){
				$firstIndex = $_POST["firstIndex"];
				$lastIndex = $_POST["lastIndex"];
				$_SESSION["jsonProduct"] = viewProduct($firstIndex,$lastIndex);
				//echo $firstIndex;
				header("Location:admin_product.php?first=$firstIndex&last=$lastIndex");
			}
		}
		else if($input == "addProduct"){
			if(isset($_POST["productName"]) && isset($_POST["productDetail"]) && isset($_POST["productPrice"]) && isset($_POST["productStock"])){
				$productName = $_POST["productName"];
				$productDetail = $_POST["productDetail"];
				$productPrice = $_POST["productPrice"];
				$productStock = $_POST["productStock"];
				
				if($productName == "" || $productDetail == "" || $productPrice == "" || $productStock == ""){
					$_SESSION["jsonProduct"] = viewProduct(getProductRows()-5,5);
					$firstIndex = getProductRows()-5;
					$lastIndex = 5;
					header("Location:admin_product.php?first=$firstIndex&last=$lastIndex&message=empty_field"); //succeed
				}
				else if($productName != "" && $productDetail != "" && $productPrice != "" && $productStock != ""){
					/* For storing image to server */
					define ("MAX_SIZE","1000"); 
					function getExtension($str) {
						$ext = $str;
						for($i = 0; $i < strlen($str); $i++){
							if($str[$i] == '.'){
								//must find how may characters left
								$l = strlen($str) - $i;
								$ext = substr($ext,$i+1,$l);
							}
						}
						return $ext;
					}
					
					function getName($str) {
						for($i = 0; $i < strlen($str); $i++){
							if($str[$i] == '.'){
								//must find how may characters left
								$str = substr($str,0,$i);
							}
						}
						return $str;
					}
					
					 $errors = 0;
					 
					if(isset($_POST['Submit'])) {
						//reads the name of the file the user submitted for uploading, image is the input type name
						$productImage = $_FILES["productImage"];
						$image=$productImage['name']; //determined already, must never be modified
						//echo "images is ".$image;

						//if it is not empty
						if ($image) {
							//input data detail into THE DATABASE
							$productRow = addProduct(addslashes($productName), addslashes($productDetail), $productPrice, $productStock, $image);
							
							if($productRow == 0){
								//get the original name of the file from the clients machine
								//get the extension of the file in a lower case format
								$extension = getExtension($image);
								$extension = strtolower($extension);
								//if it is not a known extension, we will suppose it is an error and will not  upload the file,  
								//otherwise we will do more tests
								if (($extension != "jpg") && ($extension != "jpeg") && ($extension != "png") && ($extension != "gif")) {
								//print error message
									//echo '<h1>Unknown extension!</h1>';
									$errors=1;
								}
								else{
									//get the size of the image in bytes
									$size=filesize($productImage['tmp_name']); //?a mystery

									//compare the size with the maxim size we defined and print error if bigger
									if ($size > MAX_SIZE*1024){
										//echo '<h1>You have exceeded the size limit!</h1>'; //session the message braow
										$errors=1;
									}

									$stringNamePhoto = getName($image);
									//echo $stringNamePhoto;
									$image_name = $stringNamePhoto.'.'.$extension;
									//echo "<br>";
									//echo $image_name;
									//the new name will be containing the full path where will be stored (images folder)
									$newname="Product_Photo/".$image_name;
									//we verify if the image has been uploaded, and print error instead
									$copied = copy($productImage['tmp_name'], $newname);
									if (!$copied) {
										//echo '<h1>Copy unsuccessfull!</h1>';
										$errors=1;
									}
								}
								$_SESSION["jsonProduct"] = viewProduct(getProductRows()-5,5);
								$firstIndex = getProductRows()-5;
								$lastIndex = 5;
								header("Location:admin_product.php?first=$firstIndex&last=$lastIndex"); //succeed
							}
							else{
								$_SESSION["jsonProduct"] = viewProduct(getProductRows()-5,5);
								$firstIndex = getProductRows()-5;
								$lastIndex = 5;
								header("Location:admin_product.php?first=$firstIndex&last=$lastIndex&message=image_existed"); //product name same, succeed
							}
						}
						else{
							$_SESSION["jsonProduct"] = viewProduct(getProductRows()-5,5);
							$firstIndex = getProductRows()-5;
							$lastIndex = 5;
							header("Location:admin_product.php?first=$firstIndex&last=$lastIndex&message=no_picture");
						}
					}

					//If no errors registred, print the success message
					 /* if(isset($_POST['Submit']) && !$errors) {
						$okay = "image_uploaded";
					 } */
					//} HERE
				}
			}
		}
		else if($input == "updateProduct"){
			if(isset($_POST["productName"]) && isset($_POST["productDetail"]) && isset($_POST["productPrice"]) && isset($_POST["productStock"]) && 
			isset($_POST["firstIndex"]) && isset($_POST["lastIndex"]) && isset($_POST["id"]) && isset($_POST["title"])){
				$productName = $_POST["productName"];
				$productDetail = $_POST["productDetail"];
				$productPrice = $_POST["productPrice"];
				$productStock = $_POST["productStock"];
				$firstIndex = $_POST["firstIndex"];
				$lastIndex = $_POST["lastIndex"];
				$id = $_POST["id"];
				$title = $_POST["title"];
				
				if($productName == "" || $productDetail == "" || $productPrice == "" || $productStock == ""){
					header("Location:show_product_detail.php?message=empty_field&id=$id&first=$firstIndex&last=$lastIndex");
				}
				else if($productName != "" && $productDetail != "" && $productPrice != "" && $productStock != ""){
					/* For storing image to server */
					define ("MAX_SIZE","1000"); 
					function getExtension($str) {
						$ext = $str;
						for($i = 0; $i < strlen($str); $i++){
							if($str[$i] == '.'){
								//must find how may characters left
								$l = strlen($str) - $i;
								$ext = substr($ext,$i+1,$l);
							}
						}
						return $ext;
					}
					
					function getName($str) {
						for($i = 0; $i < strlen($str); $i++){
							if($str[$i] == '.'){
								//must find how may characters left
								$str = substr($str,0,$i);
							}
						}
						return $str;
					}
					
					 $errors = 0;
					 
					if(isset($_POST['Submit'])) {
						//echo "aaa";
						//reads the name of the file the user submitted for uploading, image is the input type name
						$productImage = $_FILES["productImage"];
						$image=$productImage['name']; //determined already, must never be modified
						//echo "images is ".$image;

						//if it is not empty
						if ($image) {
							//input data detail into THE DATABASE
							$markUpdate = updateProduct(addslashes($productName), addslashes($productDetail), $productPrice, $productStock, $image, $id);
							if($markUpdate == 1){
								//get the original name of the file from the clients machine
								//get the extension of the file in a lower case format
								$extension = getExtension($image);
								$extension = strtolower($extension);
								//if it is not a known extension, we will suppose it is an error and will not  upload the file,  
								//otherwise we will do more tests
								if (($extension != "jpg") && ($extension != "jpeg") && ($extension != "png") && ($extension != "gif")) {
								//print error message
									//echo '<h1>Unknown extension!</h1>';
									$errors=1;
								}
								else{
									//get the size of the image in bytes
									$size=filesize($productImage['tmp_name']); //?a mystery

									//compare the size with the maxim size we defined and print error if bigger
									if ($size > MAX_SIZE*1024){
										//echo '<h1>You have exceeded the size limit!</h1>'; //session the message braow
										$errors=1;
									}

									$stringNamePhoto = getName($image);
									//echo $stringNamePhoto;
									$image_name = $stringNamePhoto.'.'.$extension;
									//echo "<br>";
									//echo $image_name;
									//the new name will be containing the full path where will be stored (images folder)
									$newname="Product_Photo/".$image_name;
									//we verify if the image has been uploaded, and print error instead
									$copied = copy($productImage['tmp_name'], $newname);
									if (!$copied) {
										//echo '<h1>Copy unsuccessfull!</h1>';
										$errors=1;
									}
								}
								$_SESSION["jsonProduct"] = viewProduct($firstIndex,$lastIndex);
								$_SESSION["jsonProductDetail"] = getProductDetail((int)$id);
								header("Location:show_product_detail.php?id=$id&first=$firstIndex&last=$lastIndex");
							}
							else if($markUpdate == 0){
								$_SESSION["jsonProduct"] = viewProduct($firstIndex,$lastIndex);
								$_SESSION["jsonProductDetail"] = getProductDetail((int)$id);
								header("Location:show_product_detail.php?id=$id&first=$firstIndex&last=$lastIndex&message=update_fail");
							}
						}
						else{
							//header("Location:admin_product.php?message=no_picture");
							//means you are not updating the picture
							$image = "";
							$markUpdate = updateProduct(addslashes($productName), addslashes($productDetail), $productPrice, $productStock, $image, $id);
							if($markUpdate == 1){
								//do nothing
								$_SESSION["jsonProduct"] = viewProduct($firstIndex,$lastIndex);
								$_SESSION["jsonProductDetail"] = getProductDetail((int)$id);
								header("Location:show_product_detail.php?id=$id&first=$firstIndex&last=$lastIndex");
							}
							else if($markUpdate == 0){
								$_SESSION["jsonProduct"] = viewProduct($firstIndex,$lastIndex);
								$_SESSION["jsonProductDetail"] = getProductDetail((int)$id);
								header("Location:show_product_detail.php?id=$id&first=$firstIndex&last=$lastIndex&message=update_fail");
							}
						}
					}

					//If no errors registred, print the success message
					 /* if(isset($_POST['Submit']) && !$errors) {
						$okay = "image_uploaded";
					 } */
					//} HERE
				}
			}
		}
		else if($input == "regisUser"){
			if(isset($_POST["firstName"]) && isset($_POST["lastName"]) && isset($_POST["email"]) && isset($_POST["pass"]) && isset($_POST["rePass"]) && isset($_POST["address"])
			&& isset($_POST["postalCode"]) && isset($_POST["phone"]) && isset($_POST["regency"]) && isset($_POST["subDistrict"])){
				$firstName = $_POST["firstName"];
				$lastName = $_POST["lastName"];
				$email = $_POST["email"];
				$pass = $_POST["pass"];
				$rePass = $_POST["rePass"];
				$address = $_POST["address"];
				$postalCode = $_POST["postalCode"];
				$phone = $_POST["phone"];
				$regency = $_POST["regency"];
				$subDistrict = $_POST["subDistrict"];
				
				if($firstName == "" || $lastName == "" || $email == "" || $pass == "" || $rePass == "" || $address == "" || $postalCode == "" || $phone == ""){
					header("Location:index.php?message=empty_field");
				}
				else if($firstName != "" && $lastName != "" && $email != "" && $pass != "" && $rePass != "" && $address != "" && $postalCode != "" && $phone != ""){
					//checking the uniqueness of the email address
					
					if(strpos($email,"@") == true){
						$unique = isEmailUnique($email);
						
						if($unique == true){
							//you can use the email address
							//now the password validation
							if($pass == $rePass){
								//password validation passed
								//input the data to the database then
								regisUser($firstName,$lastName,$email,$pass,$address,$postalCode,$phone,$regency,$subDistrict);
								header("Location:index.php?message=register_success");
							}
							else if($pass != $rePass){
								header("Location:index.php?message=pass_not_same");
							}
						}
						else if($unique == false){
							header("Location:index.php?message=email_exist");
						}
					}
					else if(strpos($email,"@") == false){
						header("Location:index.php?message=email_invalid");
					}
				}
			}
		}
		else if($input == "loginUser"){
			if(isset($_POST["emailLogin"]) && isset($_POST["passLogin"])){
				$email = $_POST["emailLogin"];
				$pass = $_POST["passLogin"];
				
				if($email == "" || $pass == ""){
					header("Location:index.php?message=login_empty");
				}
				else if($email != "" && $pass != ""){
					$jsonLogin = loginUser($email,$pass);
					$loginArray = json_decode($jsonLogin,true);
					
					if($loginArray[0] == false){
						header("Location:index.php?message=login_fail");
					}
					else{
						//proceed
						$_SESSION["loginArray"] = $loginArray;
						header("Location:index.php");
					}
				}
			}
		}
		else if($input == "logoutUser"){
			unset($_SESSION["loginArray"]);
			unset($_SESSION["cartName"]);
			header("Location:index.php");
		}
		else if($input == "addCart"){ //tested: working properly, but must be tested again later
			if(isset($_POST["totalBuy"]) && isset($_POST["firstIndex"]) && isset($_POST["lastIndex"]) && isset($_POST["id"]) && isset($_POST["itemPrice"]) && isset($_SESSION["loginArray"])){
				$totalBuy = $_POST["totalBuy"];
				$firstIndex = $_POST["firstIndex"];
				$lastIndex = $_POST["lastIndex"];
				$id = $_POST["id"]; //id product to be bought
				$itemPrice = $_POST["itemPrice"];
				$idUser = 0;
				$email = "";
				$cartName = "";
				$totalPrice = 0.0;
				
				if($totalBuy == ""){
					header("Location:product_detail_user.php?id=$id&first=$firstIndex&last=$lastIndex&message=empty_field");
				}
				else if($totalBuy != ""){
					if($totalBuy <= 0){
						header("Location:product_detail_user.php?id=$id&first=$firstIndex&last=$lastIndex&message=buy_zero");
					}
					else{
						if(!isset($_SESSION["cartName"])){ //cart has not yet been available
							for($i = 0; $i < sizeof($_SESSION["loginArray"]) - 1; $i++){
								$login = $_SESSION["loginArray"][$i];
								$idUser = $login["iduser"];
								$email = $login["email"];
							}
							
							$time = time();
							$date = date('Y-M-D');
							$emailId = substr($email,0,3);
							$cartName = $time.$date.strval($idUser).$emailId;
						}
						else if(isset($_SESSION["cartName"])){ //cart has been formed
							$cartName = $_SESSION["cartName"];
						}
						
						//calculate the price of the bought item
						$totalPrice = $itemPrice * $totalBuy;
						//checking the total buy again, which might be turn 0 in a sudden
						$prodNumber = checkProdNumber($id);
						
						if($prodNumber == 0){
							header("Location:product_detail_user.php?id=$id&first=$firstIndex&last=$lastIndex&message=no_stock");
						}
						else{
							//means the product number is not zero
							//check now whether the stock number is larger than the total that is bought or not, if yes, proceed, if not, bye bye
							if($prodNumber < $totalBuy){
								//we want to add 3, but there is only 2 left in the table product
								header("Location:product_detail_user.php?id=$id&first=$firstIndex&last=$lastIndex&message=stock_not_enough");
							}
							else if($prodNumber >= $totalBuy){
								//means we want to add 3, exactly 3 in table product or table product has larger than 3
								//enter the item to the shopping cart right now
								addToCart($cartName,$id,$totalBuy,$totalPrice,$idUser); //sequence of parameter: cart name, id item, total bought and the total price
								//session the cart name!!!!
								$_SESSION["cartName"] = $cartName;
								$_SESSION["jsonProductDetail"] = getProductDetail($id);
								header("Location:product_detail_user.php?id=$id&first=$firstIndex&last=$lastIndex&message=added_cart");
							}
						}
						//echo $cartName;
					}
				}
			}
		}
		else if($input == "updateCart"){ //tested: working properly, but must be tested again later
			if(isset($_POST["totalUpdate"]) && isset($_POST["firstIndex"]) && isset($_POST["lastIndex"]) && isset($_POST["totalBought"]) && isset($_POST["totalPrice"]) && isset($_POST["idItem"])){
				$totalUpdate = (int)$_POST["totalUpdate"];
				$firstIndex = $_POST["firstIndex"];
				$lastIndex = $_POST["lastIndex"];
				$totalBought = $_POST["totalBought"];
				$totalPrice = $_POST["totalPrice"];
				$idItem = $_POST["idItem"];
				$cartName = "";
				if(isset($_SESSION["cartName"])){
					$cartName = $_SESSION["cartName"];
				}
				//must check first the stock available in the database
				$stock = checkProdNumber($idItem);
				// if($stock > $totalItem){
					//stock is not zero, still possible to do some updates if it is higher
				if($totalBought > $totalUpdate){
					//means we are reducing the stock in cart, because the previous stock is bigger:
					/*
					EXAMPLE:
					in the cart, there is 6, we want to update into 5, means we want to reduce 1 and thus the 1 is returned to table product to be added again.
					in this case, although the stock in product is currently zero, it will never be a problem
					*/
					$totalNew = $totalBought - $totalUpdate; //totalNew = 6 - 5 = 1
					$totalNew = $totalNew + $stock; //basically, when we reduce items in cart, it means we add the number to the table product AGAIN!!
					//$totalNew must be added to table product, while $totalUpdate will be updated to table shopping cart detail
					
					//update process starts below:
					updateCart($idItem,$totalNew,$totalUpdate,$cartName);
					header("Location:checkCart.php?firstIndex=$firstIndex&lastIndex=$lastIndex&message=updated_cart");
				}
				else if($totalBought < $totalUpdate){
					//means we are adding the stock in cart, because the previous stock is smaller
					/*
					EXAMPLE:
					in the cart, there is 5, we want to update into 6, means we have to add 1 and thus table product reduce 1
					*/
					$totalNew = $totalUpdate - $totalBought; //totalNew = 6 - 5 = 1 and 1 is reduced from table product and added to table shopping cart
					if($stock == 0){
						//means we cannot update the items in the cart, since we want to ADD and not REDUCE
						header("Location:checkCart.php?firstIndex=$firstIndex&lastIndex=$lastIndex&message=no_stock");
					}
					else if($stock < $totalNew){ //we want to update 1 right? reduce 1 from product and add 1 to shopping cart
						//says we want to update 2, we reduce 1 from product and add 2 to shopping cart and in stock db it is only 1 left.. thus the stock is not enough and
						//we cannot do the update process
						header("Location:checkCart.php?firstIndex=$firstIndex&lastIndex=$lastIndex&message=stock_not_enough");
					}
					else if($stock >= $totalNew){
						//means we want to update 5, in the table product, there is 5 or more than 5
						//says we want to update 2, means we have to reduce 2 from product and add 2 to shopping cart
						//in stock db there is exactly 2 or maybe 3 or larger than 2,
						//thus the process can be done perfectly
						$totalNew = $stock - $totalNew;//says stock is 3 and total new is 2 (the total update that should be done), means in db will be 1 left!
						updateCart($idItem,$totalNew,$totalUpdate,$cartName);
						/*echo "total new is ".$totalNew;
						echo "total update is ".$totalUpdate;
						echo "total "*/
						header("Location:checkCart.php?firstIndex=$firstIndex&lastIndex=$lastIndex&message=updated_cart");
						//header("Location:checkCart.php?first=$firstIndex&last=$lastIndex&message=updated_cart");
					} 
				}
			}
		}
		else if($input == "checkAvailableCart"){
			if(isset($_POST["idUser"])){
				
			}
		}
		else if($input == "clearCart"){
			$cartName = "";			
			if(isset($_SESSION["cartName"]) && isset($_POST["firstIndex"]) && isset($_POST["lastIndex"])){
				$firstIndex = $_POST["firstIndex"];
				$lastIndex = $_POST["lastIndex"];
				$cartName = $_SESSION["cartName"];
				clearCart($cartName);
				//unset($_SESSION["cartName"]);
				//later must unset the session so that it will indicate that the session does not exist anymore!
				header("Location:checkCart.php?firstIndex=$firstIndex&lastIndex=$lastIndex");
			}
			/* else if(!isset($_SESSION["cartName"])){
				header("Location:checkCart.php?firstIndex=$firstIndex&lastIndex=$lastIndex&message=no_cart");
			} */
		}
		else if($input == "shipSameAddress"){
			if(isset($_POST["address"]) && isset($_POST["postalCode"]) && isset($_POST["phoneNumber"]) && isset($_POST["regency"]) && isset($_POST["subDistrict"]) && isset($_POST["delService"])
			&& isset($_POST["firstIndex"]) && isset($_POST["lastIndex"]) && isset($_POST["allPrice"])){
				$address = $_POST["address"];
				$postalCode = $_POST["postalCode"];
				$phoneNumber = $_POST["phoneNumber"];
				$regency = $_POST["regency"];
				$subDistrict = $_POST["subDistrict"];
				$firstIndex = $_POST["firstIndex"];
				$lastIndex = $_POST["lastIndex"];
				$delService = $_POST["delService"];
				$allPrice = $_POST["allPrice"];
				
				/* unset($_SESSION["address"]);
				unset($_SESSION["postalCode"]);
				unset($_SESSION["phoneNumber"]);
				unset($_SESSION["regency"]);
				unset($_SESSION["subDistrict"]);
				unset($_SESSION["delService"]);
				unset($_SESSION["allPrice"]); */
				
				$_SESSION["address"] = $address;
				$_SESSION["postalCode"] = $postalCode;
				$_SESSION["phoneNumber"] = $phoneNumber;
				$_SESSION["regency"] = $regency;
				$_SESSION["subDistrict"] = $subDistrict;
				$_SESSION["delService"] = $delService;
				$_SESSION["allPrice"] = $allPrice;
				header("Location:confirmationPage.php?firstIndex=$firstIndex&lastIndex=$lastIndex");
			}
		}
		else if($input == "shipDifAddress"){
			if(isset($_POST["address1"]) && isset($_POST["postalCode1"]) && isset($_POST["phoneNumber1"]) && isset($_POST["regency1"]) && isset($_POST["subDistrict1"]) && isset($_POST["delService1"]) 
			&& isset($_POST["firstIndex"]) && isset($_POST["lastIndex"]) && isset($_POST["allPrice"])){
				$address = $_POST["address1"];
				$postalCode = $_POST["postalCode1"];
				$phoneNumber = $_POST["phoneNumber1"];
				$regency = $_POST["regency1"];
				$subDistrict = $_POST["subDistrict1"];
				$firstIndex = $_POST["firstIndex"];
				$lastIndex = $_POST["lastIndex"];
				$delService = $_POST["delService1"];
				$allPrice = $_POST["allPrice"];
				
				if($address == "" || $postalCode == "" || $phoneNumber == ""){
					header("Location:shipping.php?firstIndex=$firstIndex&lastIndex=$lastIndex&allPrice=$allPrice&message=empty_field&allPrice=$allPrice");
				}
				else if($address != "" && $postalCode != "" && $phoneNumber != ""){
					/* echo "address is ".$address."<br>";
					echo "postal code is ".$postalCode."<br>";
					echo "phone number is ".$phoneNumber."<br>";
					echo "regency is ".$regency."<br>";
					echo "sub district is ".$subDistrict."<br>";
					echo "delivery service is ".$delService."<br>"; */
					/* unset($_SESSION["address"]);
					unset($_SESSION["postalCode"]);
					unset($_SESSION["phoneNumber"]);
					unset($_SESSION["regency"]);
					unset($_SESSION["subDistrict"]);
					unset($_SESSION["delService"]);
					unset($_SESSION["allPrice"]); */
					
					$_SESSION["address"] = $address;
					$_SESSION["postalCode"] = $postalCode;
					$_SESSION["phoneNumber"] = $phoneNumber;
					$_SESSION["regency"] = $regency;
					$_SESSION["subDistrict"] = $subDistrict;
					$_SESSION["delService"] = $delService;
					$_SESSION["allPrice"] = $allPrice;
					//echo $_SESSION["address"]."<br>";
					//echo $regency."<br>";
					//echo "AAAAAAAAAAAA GUE UDAH GILA<br>";
					//echo sizeof($_SESSION["loginArray"])."<br>";
					//echo $_SESSION["cartName"];
					header("Location:confirmationPage.php?firstIndex=$firstIndex&lastIndex=$lastIndex");
				}
			}
			else{
				echo "FAIL";
			}
		}
		else if($input == "confirmTransaction"){
			if(isset($_POST["bank"]) && isset($_SESSION["loginArray"]) && isset($_SESSION["cartName"]) && isset($_SESSION["address"]) && isset($_SESSION["postalCode"]) && 
			isset($_SESSION["phoneNumber"]) && isset($_SESSION["regency"]) && isset($_SESSION["subDistrict"]) && isset($_SESSION["delService"]) && isset($_POST["firstIndex"]) 
			&& isset($_POST["lastIndex"]) && isset($_POST["allPrice"]) && isset($_POST["shipCost"])){
				//must generate the confirmation code!
				$bank = $_POST["bank"];
				$cartName = "";
				$idUser = 0;
				$email = "";
				$allPrice = $_POST["allPrice"];
				$shipCost = $_POST["shipCost"];
				$totalPaid = $allPrice + $shipCost;
				
				if(isset($_SESSION["cartName"])){ //cart has been formed
					$cartName = $_SESSION["cartName"];
				}
				
				for($i = 0; $i < sizeof($_SESSION["loginArray"]) - 1; $i++){
					$login = $_SESSION["loginArray"][$i];
					$idUser = $login["iduser"];
					$email = $login["email"];
				}
				
				//==GENERATE THE CONFIRMATION CODE===========================
				$time = time();
				$date = date('Y-M-D');
				$emailId = substr($email,0,3);
				$cartId = substr($cartName,0,3);
				$confirmationCode = $date."--".$time."--".$emailId."--".$cartId."--".$idUser;
				//=============================================================	
				
				//==add shipping data to database
				addShippingData($_SESSION["address"],$_SESSION["postalCode"],$_SESSION["phoneNumber"],$_SESSION["regency"],$_SESSION["subDistrict"],$confirmationCode);
				
				//==add confirmation data to database
				addConfirmationData($cartName,$idUser,$allPrice,$shipCost,$totalPaid,$confirmationCode);
				
				//==generate message for the user
				//real website ==> hansprimandalachandra.com/review_transaction.php
				$lastMessage = "Thank you for shopping at our store<br><br>
				This is your confirmation code: <B><I>".$confirmationCode."</I></B><br>
				The confirmation code is used to check the status of the payment and you must also put this confirmation code in the payment message field when your are transferring the payment<br>
				Your bank account is with ".$bank."<br><br>
				The price for the items is: <B><I>".$allPrice."</I></B><br>
				The shipping cost is: <B><I>".$shipCost."</I></B><br>
				Total payment is: <B><I>".$totalPaid."</I></B><br><br>
				To review the items that you have bought, you can <a href = review_transaction.php?cartName=$cartName>click here</a><br><br><br>
				Please remember that we have a shopping policy, in which all users should pay <B><U>within 3 days</U></B> or the transaction will be considered to be canceled.<br><br>
				Below will be the data for the admin bank account, where you can transfer your money to:<br>
				BCA -- 152527762862868 -- a/n ABC<br>
				Mandiri -- 636464626469 -- a/n DFG<br>
				
				Please send message to our administrator if you have done the payment, so that our admin can check it immediately. You can send the message in the section that we have provided in 
				the section that is accessible by pressing the button <b>Check your item confirmation</b> in your front page<br><br>
				
				Best regards, Hummel Team<br>
				";
				
				//==generate the e-mail to be sent to the user
				// Always set content-type when sending HTML email
				$headers = "MIME-Version: 1.0" . "\r\n";
				$headers .= "Content-type:text/html;charset=iso-8859-1" . "\r\n";
				mail($email,"Confirmation Email",$lastMessage,$headers); //work only in server
				$_SESSION["lastMessage"] = $lastMessage;
				header("Location:transaction.php");
			}
			else{
				echo "<font color = red>The page cannot  be accessed. You might have not chosen to choose the bank service for payment</font>";
			}
		}
		else if($input == "checkAvailableCart"){
			//echo "CCCC";
			if(isset($_POST["idUser"])){
				$cartDataArray = checkAvailCart((int)$_POST["idUser"]);
				echo "aaaa";
				$_SESSION["cartDataArray"] = $cartDataArray;
				//header("Location:cartAvail.php");
			}
			else{
				///echo "bbbb";
			}
		}
		else{
			echo "Value has not been set";
		}
	}
	else if(!isset($_POST["input"])){
		//echo "Value has not been setaaaaaaaaaaa";
	}
	
	if(isset($_GET["input"])){
		$input = $_GET["input"];
		
		if($input == "getProdDetail"){
			if(isset($_GET["id"]) && isset($_GET["first"]) && isset($_GET["last"])){
				$id = $_GET["id"];
				$firstIndex = $_GET["first"];
				$lastIndex = $_GET["last"];
				$_SESSION["jsonProductDetail"] = getProductDetail((int)$id);
				header("Location:show_product_detail.php?id=$id&first=$firstIndex&last=$lastIndex");
			}
		}
		else if($input == "getProdDetailUser"){
			if(isset($_GET["id"]) && isset($_GET["first"]) && isset($_GET["last"])){
				$id = $_GET["id"];
				$firstIndex = $_GET["first"];
				$lastIndex = $_GET["last"];
				$_SESSION["jsonProductDetail"] = getProductDetail((int)$id);
				header("Location:product_detail_user.php?id=$id&first=$firstIndex&last=$lastIndex");
			}
		}
		else if($input == "removeProduct"){
			if(isset($_GET["id"]) && isset($_GET["first"]) && isset($_GET["last"]) && isset($_GET["title"])){
				$id = $_GET["id"];
				$firstIndex = $_GET["first"]-1;
				$lastIndex = $_GET["last"];
				$title = "Product_Photo/".$_GET["title"];
				unlink($title);
				$_SESSION["productName"] = removeProduct($id);
				$_SESSION["jsonProduct"] = viewProduct($firstIndex,$lastIndex);
				header("Location:admin_product.php?first=$firstIndex&last=$lastIndex");
			}
		}
		else if($input == "viewProduct"){
			if(isset($_GET["firstIndex"]) && isset($_GET["lastIndex"])){
				$firstIndex = $_GET["firstIndex"];
				$lastIndex = $_GET["lastIndex"];
				$_SESSION["jsonProduct"] = viewProduct($firstIndex,$lastIndex);
				//echo $firstIndex;
				header("Location:product.php?message=show_product&first=$firstIndex&last=$lastIndex");
			}
		}
		else if($input == "removeCart"){ //tested: working properly, but must be tested again later
			if(isset($_GET["idItem"]) && isset($_GET["totalStock"]) && isset($_GET["firstIndex"]) && isset($_GET["lastIndex"])){
				$idItem = $_GET["idItem"];
				$totalStock = $_GET["totalStock"];
				$firstIndex = $_GET["firstIndex"];
				$lastIndex = $_GET["lastIndex"];	
				$cartName = "";
				
				if(isset($_SESSION["cartName"])){
					$cartName = $_SESSION["cartName"];
				}
				
				removeCart($idItem,$totalStock,$cartName);
				header("Location:checkCart.php?firstIndex=$firstIndex&lastIndex=$lastIndex&message=removed_cart");
			}
		}
		else if($input == "setCart"){
			if(isset($_GET["cartName"])){
				$_SESSION["cartName"] = $_GET["cartName"];
				header("Location:cartAvail.php?message=loaded_cart");
			}
		}
		else if($input == "deleteCart"){
			if(isset($_GET["idCart"]) && isset($_GET["idUser"])){
				$markRemove = deleteCart((int)$_GET["idCart"],$_SESSION["cartName"],(int)$_GET["idUser"]);
				$_SESSION["cartDataArray"] = checkAvailCart((int)$_GET["idUser"]);
				
				if($markRemove == 1){
					//header("Location:cartAvail.php?message=current_cart_emptied");
					unset($_SESSION["cartName"]);
					header("Location:cartAvail.php?message=cart_destroyed");
				}
				else if($markRemove == 0){
					//this conditional means we remove the cart that are not loaded
					header("Location:cartAvail.php?message=cart_destroyed");
				}
			}
		}
	}
?>