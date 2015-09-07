<!DOCTYPE HTML>
<html>
<body>
<h1>Cards</h1>
<?php
	require_once('PassportPayments.php');
	$appId = "37Z8ZDAZE8N";
	$appSecret = "b2578c560673697eb49834b05a70aa1a";
	$publicKey="37XI8CVAQSK";
	$endPoint = 'https://sandbox.passportpayments.com';
	$pp = new PassportPayments($appId, $appSecret, $publicKey, $endPoint);
?>

<?php
$custid= $_GET["id"];
?>



<form method="get" action= "addCard.php?">
	<input type="submit" name="" value="Add Card">
	<input type="hidden" name="cid" value="<?php echo $custid?>">
</form>

<h2>  List of all the associated cards</h2>
<?php
//Printing all cards

$customerId= $_GET["id"];//value from prev page, via link abc.php?name=value form

	$customer= $pp->getCustomer($customerId);

	$customerEncoded=json_encode($customer);//encoding to further decode, string form

	$customerDecoded= json_decode($customerEncoded,true);//converted into associative arrays

	for($i=0;$i<count($customerDecoded["data"]["card"]);$i++){
			$tempo= $customerDecoded["data"]["card"][$i]["cardid"];
echo '<a href="listOfTransactions.php?cdid='.$tempo.'&cid='.$customerId.'">'. $customerDecoded["data"]["card"][$i]["tail"]. '</a>';
	echo '<br><br>';
	}

?>
</body>
</html>
