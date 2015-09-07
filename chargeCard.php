
<?php
	if($_POST){
		
		if(empty($_POST["amount"])){
			$amountError="Missing amount";
		}
		else{
			$params['amount']= test_input($_POST["amount"]);
		}

		$params['currency']="USD";
			
	}

function test_input($data){
	$data= trim($data);// remove whitespaces and predefined chars(&lt->'<') - both ends
	$data= htmlspecialchars($data);// converts predefined chars and process the data and then returns. 
	
	return $data;
}

?>

<?php

if($_POST && $amountError=="" && $currencyError==""){
	require_once('PassportPayments.php');
	$appId = "37Z8ZDAZE8N";
	$appSecret = "b2578c560673697eb49834b05a70aa1a";
	$publicKey="37XI8CVAQSK";
	$endPoint = 'https://sandbox.passportpayments.com';
	$pp = new PassportPayments($appId, $appSecret, $publicKey, $endPoint);

	
	$cardId= $_POST["cardId"];
	

	$resp= $pp->captureByCardId($cardId,$params);
	if($resp->status!=200){
		echo "message: ".$resp->message."<br>";		
		
	}
	else{
		
		header('Location: listOfTransactions.php?cdid='.$cardId.'&cid='.$customerId.'');//no status exists-no error-success

	}
}
else{
	if($_GET)
			$cardId=$_GET["cardId"] ;
		else
			$cardId=$_POST["cardId"];	

?>

<!DOCTYPE HTML>
<html>
<body>
<h1> Charge Card </h1>

<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
	<input type="hidden" name="cardId" value="<?php echo $cardId;?>">
	
	Amount*: <input type="number" name="amount" value="<?php echo $params['amount'];?>">
	<span class="error"> <?php echo $amountError;?> </span>
	<br><br><br>
	
	
	<input type="submit" name="submit" value="Submit">

</form>
</body>
</html>

<?php 
}?>
