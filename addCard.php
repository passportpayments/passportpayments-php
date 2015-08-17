

<?php
	if($_POST){
//card number
		if(empty($_POST["cardnumber"])){
			$cardnumberError="Missing card number";
		}
		else{
			$cardnumber= test_input($_POST["cardnumber"]);
			if(!preg_match("/^[0-9]{16}$/",$cardnumber))
				$cardnumberError="only 0-9 allowed";
		}

//expiry month
		if(empty($_POST["expmonth"])){
			$expmonthError="Missing expiry month";
		}
		else{
			$expmonth= test_input($_POST["expmonth"]);
			if(!preg_match("/^[0-9]{2}$/",$expmonth))
				$expmonthError="only 0-9 allowed";
		}
//expiry year
		if(empty($_POST["expyear"])){
			$expyearError="Missing expiry year";
		}
		else{
			$expyear= test_input($_POST["expyear"]);
			if(!preg_match("/^[0-9]{2}$/",$expyear))
				$expyearError="only 0-9 allowed";
		}
//cvv
		if(empty($_POST["cvv"])){
			$cvvError="Missing cvv";
		}
		else{
			$cvv= test_input($_POST["cvv"]);
			if(!preg_match("/^[0-9]{3}$/",$cvv))
				$cvvError="only 0-9 allowed";
		}
//name on card
		if(empty($_POST["nameoncard"])){
			$nameoncardError="Missing name on card";
		}
		else{
			$nameoncard= test_input($_POST["nameoncard"]);

			if(!preg_match("/^[a-zA-Z ]*$/",$nameoncard)){
				$nameoncardError= "Only a-z, A-Z and spaces allowed";
			}
		}

		
	}

function test_input($data){
	$data= trim($data);// remove whitespaces and predefined chars(&lt->'<') - both ends
	$data= htmlspecialchars($data);// converts predefined chars and process the data and then returns. 
	
	return $data;
}
?>
<?php
if($_POST && $cardnumberError=="" && $expmonthError=="" && $expyearError=="" && $cvvError==""&& $nameoncardError==""){
	require_once('PassportPayments.php');
	$appId = "37Z8ZDAZE8N";
	$appSecret = "b2578c560673697eb49834b05a70aa1a";
	$publicKey="37XI8CVAQSK";
	$endPoint = 'https://sandbox.passportpayments.com';
	$pp = new PassportPayments($appId, $appSecret, $publicKey, $endPoint);
	
	$resp= $pp->getCardToken($cardnumber, $expmonth, $expyear,$cvv, $nameoncard);

	if($resp->status!=200)
		echo "message:". $resp->message ."getTokenErrorMsg". "<br>";
	else{
		$cardToken= $resp->data->card_tmptoken;
		
		$customerId= $_POST["cid"];
	
		$add_card= $pp->addCard($customerId,$cardToken);
		
		if($add_card->status!=200){
			echo "message: ".$add_card->message."CardError"."<br>";
		}
		else{
			header('Location: listofCards.php?id='.$customerId.'');
		}
	}
	
}
else{
if($_GET)
			$customerId=$_GET["cid"] ;
		else
			$customerId=$_POST["cid"];	
		
?>
<!DOCTYPE HTML>
<html>
<body>
<h1> Add Card </h1>

<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
	
	<input type="hidden" name="cid" value="<?php echo $customerId?>">

	CardNumber*: <input type="number" name="cardnumber" value="<?php echo $cardnumber;?>">
	<span class="error"> <?php echo $cardnumberError;?> </span>
	<br><br><br>
	
	ExpiryMonth*: <input type="number" name="expmonth" value="<?php echo $expmonth;?>">
	<span class="error"> <?php echo $expmonthError;?> </span>
	<br><br><br>

	ExpiryYear*: <input type="number" name="expyear" value="<?php echo $expyear;?>">
	<span class="error"> <?php echo $expyearError;?> </span>
	<br><br><br>

	CVV*: <input type="number" name="cvv" value="<?php echo $cvv;?>">
	<span class="error"> <?php echo $cvvError;?> </span>
	<br><br><br>

	NameOnCard*: <input type="text" name="nameoncard" value="<?php echo $nameoncard;?>">
	<span class="error"> <?php echo $nameoncardError;?> </span>
	<br><br><br>

	<input type="submit" name="submit" value="Submit">

</form>
</body>
</html>

<?php
}?>
	

