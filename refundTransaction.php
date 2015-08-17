<?php
	require_once('PassportPayments.php');
	$appId = "37Z8ZDAZE8N";
	$appSecret = "b2578c560673697eb49834b05a70aa1a";
	$publicKey="37XI8CVAQSK";
	$endPoint = 'https://sandbox.passportpayments.com';
	$pp = new PassportPayments($appId, $appSecret, $publicKey, $endPoint);

$cardId= $_POST["cardid"];
$customerId=$_POST["cid"];

	$transactionId= $_POST["transaction_id"];
	$amount=0;

	$resp= $pp->transactionRefund($transactionId, $amount);
	if($resp->status!=200){
		echo "message: ".$resp->message;
	}
	else{
		header('Location: listOfTransactions.php?cdid='.$cardId.'&cid='.$customerId.'');
	}
?>
