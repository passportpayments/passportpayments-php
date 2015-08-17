<!DOCTYPE HTML>
<html>
<body>
<h1>Transactions</h1>
<?php
	require_once('PassportPayments.php');
	$appId = "37Z8ZDAZE8N";
	$appSecret = "b2578c560673697eb49834b05a70aa1a";
	$publicKey="37XI8CVAQSK";
	$endPoint = 'https://sandbox.passportpayments.com';
	$pp = new PassportPayments($appId, $appSecret, $publicKey, $endPoint);
?>
<?php
$cardId= $_GET["cdid"];
$customerId= $_GET["cid"];

?>

<form method="get" action= "chargeCard.php">
	<input type="submit" name="" value="Charge Card">
	<input type="hidden" name="cardId" value="<?php echo $cardId;?>">
	<input type="hidden" name="cid" value="<?php echo $customerId;?>">
</form>


<h1> List of all associated Transactions </h1>

<?php

//Printing all transactions
	$trans= $pp->getAllTransactions($customerId);
	$transEncoded= json_encode($trans);
	//echo $transEncoded;	
	$transDecoded= json_decode($transEncoded,true);
	
	for($i=0;$i<count($transDecoded["data"]);$i++){
		
		$transId= $transDecoded["data"][$i]["transaction_id"];
		echo $transId."	";

//refund form for every transaction

		echo '
<form method="post" action= "refundTransaction.php">
	<input type="submit" name="" value="Refund Transaction">
	<input type="hidden" name="transaction_id" value="'.$transId.'"> 
	<input type="hidden" name="cid" value="'.$customerId.'">
		
	<input type="hidden" name="cardid" value="'.$cardId.'">
</form>
';
	//echo "testing";
		echo "<br><br>";
	}	
	
?>
</body>
</html>

