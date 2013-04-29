<?php session_start();
	include("hummel_model.php");
	if(isset($_POST["input"])){
		$input = $_POST["input"];
		//PROBLEM DEFINED: I GUESS _PST AND $_GET REALLY HAS A LIMITATION, IF UNREASONABLY CANNOT GO INTO ONE CONTROLLER, JUST CREATE NEW ONE
		if($input == "checkAvailableCart"){
			if(isset($_POST["idUser"])){
				$cartDataArray = checkAvailCart((int)$_POST["idUser"]);
				$_SESSION["cartDataArray"] = $cartDataArray;
				header("Location:cartAvail.php");
			}
		}
		else if($input == "checkConfirm"){
			if(isset($_POST["confirmCode"])){
				$confirmCode = $_POST["confirmCode"];
				
				if($confirmCode == ""){
					header("Location:checkConfirmation.php?message=empty_field");
				}
				else if($confirmCode != ""){
					$status = checkConfirmationStatus($confirmCode);
					$itemConfirmArray = checkConfirmationItem($confirmCode);
					$addressArray = checkConfirmationAddress($confirmCode);
					
					$_SESSION["status"] = $status;
					$_SESSION["itemConfirmArray"] = $itemConfirmArray;
					$_SESSION["addressArray"] = $addressArray;
					
					header("Location:checkConfirmation.php?confirmCode=$confirmCode");
				}
			}
		}
		else if($input == "sendConfirmMessage"){
			if(isset($_POST["confirmCodeMessage"]) && isset($_POST["confirmMessages"])){
				$confirmCode = $_POST["confirmCodeMessage"];
				$confirmMessage = $_POST["confirmMessages"];
				
				if($confirmCode == "" || $confirmMessage == ""){
					header("Location:checkConfirmation.php?message=empty_field");
				}
				else if($confirmCode != "" && $confirmMessage !=""){
					sendConfirmMessage($confirmCode,$confirmMessage);
					header("Location:checkConfirmation.php?message=message_sent");
				}
			}
		}
		else if($input == "viewMessage"){
			if(isset($_POST["firstMessage"])){
				$firstMessage = $_POST["firstMessage"];
				$totalView = 5;
				$_SESSION["messageArray"] = viewMessages($firstMessage,$totalView);
				header("Location:admin_transaction.php?firstMessage=$firstMessage&totalView=$totalView");
			}
		}
		else if($input == "checkTransactionStatus"){
			if(isset($_POST["confirmCode"]) && isset($_POST["firstMessage"]) && isset($_POST["totalView"]) && isset($_POST["id"])){
				$confirmCode = $_POST["confirmCode"];
				$firstMessage = $_POST["firstMessage"];
				$totalView = $_POST["totalView"];
				$id = $_POST["id"];
				
				if(!isset($_SESSION["itemConfirmArray"]) && !isset($_SESSION["userArray"])){
					$itemConfirmArray = checkConfirmationItem($confirmCode);
					
					$idCart = 0;
					for($i = 0; $i < sizeof($itemConfirmArray) - 1; $i++){
						$item = $itemConfirmArray[$i];
						$idCart = $item["idshopping_cart"]; //definetely be the same for all
					}
					
					$userArray = getTheUser($idCart);
					
					$_SESSION["itemConfirmArray"] = $itemConfirmArray;
					$_SESSION["userArray"] = $userArray;
				}
				else if(isset($_SESSION["itemConfirmArray"]) && isset($_SESSION["userArray"])){
					unset($_SESSION["itemConfirmArray"]);
					unset($_SESSION["userArray"]);
				}
				header("Location:show_message_detail.php?id=$id&firstMessage=$firstMessage&totalView=$totalView");
			}
		}
		else if($input == "sendAccomplished"){
			if(isset($_POST["confirmCode"]) && isset($_POST["firstMessage"]) && isset($_POST["totalView"]) && isset($_POST["id"]) && isset($_POST["email"])){
				$confirmCode = $_POST["confirmCode"];
				$firstMessage = $_POST["firstMessage"];
				$totalView = $_POST["totalView"];
				$id = $_POST["id"];
				$email = $_POST["email"];
				
				updatePaymentStatus($confirmCode);
				
				$lastMessage = "Dear valued customer<br>As we have checked the status payment according to the confirmation code <b>".$confirmCode."</b><br>
				We hereby state that your payment has been accomplished. Thank you for shopping at our online store. We will ship the items soon and will inform about the shipping code later.<br><br>
				Best regards, Hummel Team<br>
				";
				
				//==generate the e-mail to be sent to the user
				// Always set content-type when sending HTML email
				$headers = "MIME-Version: 1.0" . "\r\n";
				$headers .= "Content-type:text/html;charset=iso-8859-1" . "\r\n";
				//mail($email,"Confirmation Email",$lastMessage,$headers); //work only in server  activate it later
				$_SESSION["lastMessage"] = $lastMessage;
				header("Location:show_message_detail.php?id=$id&firstMessage=$firstMessage&totalView=$totalView&message=email_sent");
			}
		}
		else if($input == "sendUnaccomplished"){
			if(isset($_POST["confirmCode"]) && isset($_POST["firstMessage"]) && isset($_POST["totalView"]) && isset($_POST["id"]) && isset($_POST["email"])){
				$confirmCode = $_POST["confirmCode"];
				$firstMessage = $_POST["firstMessage"];
				$totalView = $_POST["totalView"];
				$id = $_POST["id"];
				$email = $_POST["email"];
				
				$lastMessage = "Dear valued customer<br>As we have checked the status payment according to the confirmation code <b>".$confirmCode."</b><br>
				We hereby state that your payment has not yet been accomplished. Please check again and then inform us later.<br><br>
				Best regards, Hummel Team<br>
				";
				
				//==generate the e-mail to be sent to the user
				// Always set content-type when sending HTML email
				$headers = "MIME-Version: 1.0" . "\r\n";
				$headers .= "Content-type:text/html;charset=iso-8859-1" . "\r\n";
				//mail($email,"Confirmation Email",$lastMessage,$headers); //work only in server activate it later
				$_SESSION["lastMessage"] = $lastMessage;
				//echo "aaa";
				header("Location:show_message_detail.php?id=$id&firstMessage=$firstMessage&totalView=$totalView&message=email_sent");
			}
		}
		else if($input == "check3DaysCart"){
			if(isset($_POST["firstMessage"]) && isset($_POST["totalView"])){
				$firstMessage = $_POST["firstMessage"];
				$totalView = $_POST["totalView"];
				
				check3DaysCart();
				header("Location:admin_transaction.php?firstMessage=$firstMessage&totalView=$totalView&message=3days_deleted");
			}
		}
		else if($input == "check3DaysConfirmed"){
			if(isset($_POST["firstMessage"]) && isset($_POST["totalView"])){
				$firstMessage = $_POST["firstMessage"];
				$totalView = $_POST["totalView"];
				
				check3DaysConfirm();
				header("Location:admin_transaction.php?firstMessage=$firstMessage&totalView=$totalView&message=3daysconfirm_deleted");
			}
		}
		else if($input == "sendShipping"){
			if(isset($_POST["firstMessage"]) && isset($_POST["totalView"]) && isset($_POST["confirmCode"]) && isset($_POST["shippingCode"]) && isset($_POST["id"])){
				$firstMessage = $_POST["firstMessage"];
				$totalView = $_POST["totalView"];
				$confirmCode = $_POST["confirmCode"];
				$shippingCode = $_POST["shippingCode"]; //REQUIRED
				$idMessage = $_POST["id"];
				
				if($shippingCode == ""){
					header("Location:show_message_detail.php?id=$idMessage&firstMessage=$firstMessage&totalView=$totalView&message=empty_field");
				}
				else if($shippingCode != ""){
					$status = addShipping($confirmCode,$shippingCode);
					
					if($status == "Shipping Code Entered"){
						header("Location:show_message_detail.php?id=$idMessage&firstMessage=$firstMessage&totalView=$totalView&message=ship_done");
					}
					else if($status == "Shipping Success"){
						$lastMessage = "Dear valued customer<br>We hereby state that we have shipped your items to the address you stated. This is your shipping code <b>".$shippingCode."</b><br>
						You can track the items in JNE by inputting the shipping code in the website.<br><br>
						Best regards, Hummel Team<br>
						";
						
						//==generate the e-mail to be sent to the user
						// Always set content-type when sending HTML email
						$headers = "MIME-Version: 1.0" . "\r\n";
						$headers .= "Content-type:text/html;charset=iso-8859-1" . "\r\n";
						//mail($email,"Shipping Email",$lastMessage,$headers); //work only in server activate it later
						$_SESSION["lastMessage"] = $lastMessage;
						header("Location:show_message_detail.php?id=$idMessage&firstMessage=$firstMessage&totalView=$totalView&message=ship_succeess");
					}
				}
			}
		}
		else if($input == "checkItemsShipped"){
			if(isset($_POST["firstMessage"]) && isset($_POST["totalView"]) && isset($_POST["shipCode"])){
				$firstMessage = $_POST["firstMessage"];
				$totalView = $_POST["totalView"];
				$shipCode = $_POST["shipCode"]; //REQUIRED!!
				
				if($shipCode == ""){
					header("Location:admin_transaction.php?firstMessage=$firstMessage&totalView=$totalView&message=empty_field");
				}
				else if($shipCode != ""){
					$_SESSION["itemArray"] = checkItemsShipped($shipCode);
					header("Location:admin_transaction.php?firstMessage=$firstMessage&totalView=$totalView");
				}
			}
		}
		else if($input == "logoutAdmin"){
			session_destroy();
			header("Location:admin_index.php?message=logged_out");
		}
		else if($input == "reviewItem"){
			if(isset($_POST["emailLogin"]) && isset($_POST["passLogin"]) && isset($_POST["cartName"])){
				$email = $_POST["emailLogin"];
				$pass = $_POST["passLogin"];
				$cartName = $_POST["cartName"];
				
				if($email == "" || $pass == ""){
					header("Location:review_transaction.php?message=login_empty&cartName=$cartName");
				}
				else if($email != "" && $pass != ""){
					$jsonLogin = loginUser($email,$pass);
					$loginArray = json_decode($jsonLogin,true);
					
					if($loginArray[0] == false){
						header("Location:review_transaction.php?message=login_fail&cartName=$cartName");
					}
					else{
						//proceed
						$jsonCart = viewCart($cartName);
						$cartArray = json_decode($jsonCart,true);
						//echo jsonCart;
						$_SESSION["cartArray"] = $cartArray;
						header("Location:review_transaction.php?cartName=$cartName");
					}
				}
			}
		}
	}
	
	if(isset($_GET["input"])){
		$input = $_GET["input"];
		
		if($input == "sendConfirmMessage"){
			if(isset($_GET["confirmCodeMessage"]) && isset($_GET["confirmMessages"])){
				$confirmCode = $_GET["confirmCodeMessage"];
				$confirmMessage = $_GET["confirmMessages"];
				//echo "Message is ".$confirmMessage;
				//sendConfirmMessage($confirmCode,$confirmMessage);
				//header("Location:checkConfirmation.php?message=message_sent");
			}
		}
		else if($input == "getMessageDetail"){
			if(isset($_GET["id"]) && isset($_GET["firstMessage"]) && isset($_GET["totalView"])){
				$id = $_GET["id"];
				$firstMessage = $_GET["firstMessage"];
				$totalView = $_GET["totalView"];
				$_SESSION["messageDetailArray"] = getMessageDetail((int)$id);
				header("Location:show_message_detail.php?id=$id&firstMessage=$firstMessage&totalView=$totalView");
			}
		}
	}
?>