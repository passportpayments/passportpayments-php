
<?php

	if($_POST){
	$flag=0;
//FIRST NAME		
		if(empty($_POST["firstname"])){
			$firstnameError="Missing first name";
		}
		else{
			$firstname= test_input($_POST["firstname"]);//checking for characters other than alphabets, spaces.

			if(!preg_match("/^[a-zA-Z ]*$/",$firstname)){
				$firstnameError= "Only a-z, A-Z and spaces allowed";
			}
		}
//LAST NAME

		if(empty($_POST["lastname"])){
			$lastname="";
		}
		else{
			$lastname= test_input($_POST["lastname"]);//checking for characters other than alphabets, spaces.

			if(!preg_match("/^[a-zA-Z ]*$/",$lastname)){
				$lastnameError= "Only a-z, A-Z and spaces allowed";
			}
		}
		
//EMAIL
		if(empty($_POST["email"])){
			$emailError="Email required";
		}
		else{
			$email= test_input($_POST["email"]);
			if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
				$emailError= "Invalid email";
			}
		}

//PHONE
		if(empty($_POST["phone"])){
			$phone="";
		}
		else{
			$phone= test_input($_POST["phone"]);
			if(preg_match("/^[0-9]{2}-[0-9]{10}$/",$phone))
				$phoneError="only 0-9 are allowed";
		}



	}

function test_input($data){
	$data= trim($data);// remove whitespaces and predefined chars(&lt->'<') - both ends
	$data= htmlspecialchars($data);// converts predefined chars and process the data and then returns. 
	
	return $data;
}

?>
<?php
if($_POST && $firstnameError=="" && $lastnameError=="" && $emailError=="" && $phoneError==""){
	require_once('PassportPayments.php');
	$appId = "37Z8ZDAZE8N";
	$appSecret = "b2578c560673697eb49834b05a70aa1a";
	$publicKey="37XI8CVAQSK";
	$endPoint = 'https://sandbox.passportpayments.com';
	$pp = new PassportPayments($appId, $appSecret, $publicKey, $endPoint);

	$customerAdd= $pp->saveCustomer($firstname, $lastname,$email, $phone, "");
	
	if($customerAdd->status!=200)
		echo "message: ".$customerAdd->message."<br>";
	else
		header('Location: home.php');

}
else{
?>
<!DOCTYPE HTML>
<html>
<body>

<h1>Add a Customer</h1>

<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">


	Firstname: <input type="text" name="firstname" value="<?php echo $firstname;?>">
	<span class="error"> <?php echo $firstnameError;?> </span>
	<br><br><br>

	Lastname: <input type="text" name="lastname" value="<?php echo $lastname;?>">
	<span class="error"> <?php echo $lastnameError;?> </span>
	<br><br><br>

	Email: <input type="text" name="email" value="<?php echo $email;?>">
	<span class="error"> <?php echo $emailError;?> </span>
	<br><br><br>

	Phone: <input type="number" name="phone" value="<?php echo $phone;?>">
	<span class="error"> <?php echo $phoneError;?> </span>
	<br><br><br>
	

	<input type="submit" name="submit" value="Submit">
</form>

</body>
</html>
<?php
}?>
