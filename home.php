<!DOCTYPE HTML>
<html>
<body>
<h1>HOME PAGE</h1>

<form action= "addCustomer.php">
	<input type="submit" name="" value="Add a customer">

</form>

<h2>  List of all the customers </h2>
<?php
//Printing all customers
	require_once('PassportPayments.php');
	$appId = "37Z8ZDAZE8N";
	$appSecret = "b2578c560673697eb49834b05a70aa1a";
	$publicKey="37XI8CVAQSK";
	$endPoint = 'https://sandbox.passportpayments.com';
	$pp = new PassportPayments($appId, $appSecret, $publicKey, $endPoint);

$customers= $pp->getAllCustomers();//decoded, objects form
$encoded= json_encode($customers);//encoding to further decode, string form
$decoded=json_decode($encoded,true);//converted into associative arrays

	for($i=0;$i<count($customers->data);$i++){
			
		$val= $decoded["data"][$i]["id"];
		
		echo '<a href="listofCards.php?id='.$val.'">'. $decoded["data"][$i]["firstname"] . '</a>';
		echo '<br><br>';

	}

?>
</body>
</html>
