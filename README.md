PassportPayments PHP Wrapper Docs
=================================

### Initializing the API :

```php
require_once('PassportPayments.php');
$appId = ”xxxxxxxxx”;
$appSecret = "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx";
$endPoint = 'https://sandbox.passportpayments.com';
$pp = new PassportPayments($appId, $appSecret, $publicKey, $endPoint);
```

* $endPoint is by default pointed to sandbox. So, if you don’t pass endPoint, it’ll take you to the sandbox. If you are on live mode, and using live mode credentials, then use 'https://api.passportpayments.com' as the endpoint.


### Get card Token: 
Get the card token like this -
```php
$cardnumber = "4111111111111111";
$expmonth = "01";
$expyear = "23";
$cvv = "123";
$nameoncard = "Gilderoy Lockhart";
$resp = $pp->getCardToken( $cardnumber, $expmonth, $expyear, $cvv, $nameoncard );
```

```json
{
    "data" : {
        "card_tmptoken" : "tok_xxxxxxxxxxx"
    },
    "message" : "Use the temporary card token to make one time payment or associate with any customer.",
    "status" : 200
}
```
Here you get the customer id. Now you can perform all the customer based operations with this id.



### Save Customer : 
Now you can add a customer like this -
```php
$firstname = "Harry";
$lastname = "Potter";
$email = "harry@hogwarts.edu";
$phone = "";
$cardTempToken = “”;
$customerAdd = $pp->saveCustomer ($firstname, $lastname, $email, $phone, $cardTempToken);
```

* $lastname, $phone, $cardTempToken these are optional parameters, so you can pass them as blank, just like it’s done for $phone in the example.
* If you pass $cardTempToken (that you’ll get from passportpayments-1.0.0.min.js after submitting the card details), then it’ll be assign the card with the customer or you can associate a card with the customer at a lter stage as well. See [Associate a card with a customer](#) for the same.

```json
{
    "data": {
        "customer_id": "cus_xxxxxxxxxxxxx"
    },
    "message": "Customer added successfully.",
    "status": 200
}
```
Here you get the customer id. Now you can perform all the customer based operations with this id.

### Update Customer : 
Since you have added a customer, you might want to update his details -
```php
$customerId = ‘cus_xxxxxxxxxxxxx’;
$update = array();
$update['phone'] = ‘8334900155’;
$customerUpdate = $pp->updateCustomer ($customerId, $update);
```

* $customerId is the id that you got while adding the customer.
* $update is an array where you can pass the updates for your customers.
* The supported parameters for updates are : ‘firstname’, ‘lastname’, ‘email’, ‘phone’.
* If you pass blank string i.e. ‘’, for any of them, then that parameter will not be updated.
* A success response will look like below with the update info:

```json
{
    "data": {
        "id": "cus_xxxxxxxxxx",
        "firstname": "Harry",
        "lastname": "Potter",
        "name": "Harry Potter",
        "email": "harry@hogwarts.edu",
        "phone": "8334900155",
        "create_date": "Nov 27, 2014",
        "card": []
    },
    "message": "",
    "status": 200
}
```

* Now we see the updated phone number.
* Also, you can see, the card field is empty, because there is no card associated with the customer yet. See [Associate a card with a customer](#) section to associate a customer with a card.

### Associate a card with a customer
To associate a card with a customer you need the card token. Click here to know the procedure of getting the card token.
Once you have the card toke, you can associate the card with any one of your customer. That’ll save the card for future use.
```php
$customerId = 'cus_xxxxxxxxxxxxx';
$cardToken = 'tok_xxxxxxxxxxxxx';
$add_card = $pp->addCard ($customerId, $cardToken);
```

* This doesn’t mean that you are saving any confidential data or PCI details, that you shouldn’t save. You are not saving the card details directly, so no reason to be worried. You are safe from PCI legal issues, we handle it for you.
* The $cardToken is temporary, and valid for limited time span.

```json
{
    "data": {
        "tail": "1111",
        "cardtype": "Visa",
        "default": "0",
        "expmonth": "01",
        "expyear": "2022",
        "cardkey": "card_xxxxxxxxxxxxx",
        "customerid": "cus_xxxxxxxxxxxxx"
    },
    "message": "This card added successfully.",
    "status": 200
}
```

* cardkey is the card identifier, that can be used for making future payments.

### Update card
To update a card associated with the customer

```php
$customerId = 'cus_xxxxxxxxxxxxx';
$cardId = 'card_xxxxxxxxxxxxx';
$updates = array();
$updates['expmonth'] = '01';
$updates['expyear'] = '2019';
$cardUpdate = $pp->updateCard ($customerId, $cardId, $updates);
```

* $updates is an array, and the allowed parameters for updates are: ‘expmonth’, ‘expyear’
      
```json
{
    "data": "",
    "message": "Card updated successfully!",
    "status": 200
}
```

### Dissociate card:
To dissociate a card from the customer:

```php
$customerId = 'cus_xxxxxxxxxxxxx';
$cardId = 'card_xxxxxxxxxxxxx';
$cardDelete = $pp->deleteCard($customerId, $cardId);
```
Response:

```json
{
    "data": "",
    "message": "Card is deleted!",
    "status": 200
}
```

### Get all customers:
To get the details of all the customers you have on board:
```php
$customers = $pp->getAllCustomers();
```
Response:

```json
{
    "data": [
        {
            "id": "cus_xxxxxxxxxxxxx",
            "firstname": "Harry",
            "lastname": "Potter",
            "name": "Harry Potter",
            "email": "harry@hogwarts.edu",
            "phone": "8334900155",
            "card": [
                {
                    "tail": "4242",
                    "cardtype": "Visa",
                    "default": "1",
                    "expmonth": "01",
                    "expyear": "2019",
                    "cardid": "card_xxxxxxxxxxxxx"
                }
            ]
        },
        {
            "id": "cus_yyyyyyyyyyyyy",
            "firstname": "Authur",
            "lastname": "Weasley",
            "name": "Aurthur Weasley",
            "email": "aurthur@magicministry.gov.uk",
            "phone": "3191922199",
            "card": []
        }
    ],
    "message": "",
    "status": 200
}
```

### Get customer info:
To get customer details -

```php
$customerId = 'cus_548029ab823a2';
$customer = $pp->getCustomer($customerId);
```

Response :

```json
{
    "data": {
        "id": "cus_548029ab823a2",
        "firstname": "Passport",
        "lastname": "Admin",
        "name": "Passport Admin",
        "email": "admin@passportparking.com",
        "phone": null,
        "create_date": "Dec 4, 2014",
        "card": [
            {
                "tail": "1111",
                "cardtype": "Visa",
                "default": "1",
                "expmonth": "12",
                "expyear": "2016",
                "cardid": "card_548029aac1ff5"
            }
        ]
    },
    "message": "",
    "status": 200
}
```

### Delete a customer:

```php
$customerId = 'cus_yyyyyyyyyyyyy';
$customer = $pp->deleteCustomer($customerId);
```

Response :

```json
{
    "status": 200,
    "data": [],
    "message": "Cusotmer deactivated successfully!"
}
```

### Charge a card

There are 2 ways to do this.
* Charging the card using the card id that we get during associating a card 
* Using the temporary token to charge the card directly.

```php
	//Charging card by card id:
	$cardId = 'card_xxxxxxxxxxxxxx';
	$amountInCent = 0;
	$productId = 'prod_xxxxxxxxxxxxx';
	$producQuant = 2;
	$resp = $pp->captureByCardId($cardId, $amountInCent, $productId, $producQuant);
OR
	//Chargin card by card temporary token:
	$cartoken = 'tok_xxxxxxxxxxxxxx';
	$amountInCent = 0;
	$productId = 'prod_xxxxxxxxxxxxx';
	$producQuant = 2;
	$resp = $pp->captureByCardTmpToken($cardId, $amountInCent, $productId, $producQuant);
```
Response:
       
 ```json
{
    "status": 200,
    "data": {
        "transactionid": "tran_54807f698f64d"
    }
}
 ```

* If you give $amountInCent value, then it’ll have more priority and that much amount will be charged, else the rate of the product multiplied by product quantity will be charged.
* transaction id is returned which can be used to refund the transaction if required.

### Refund transaction

In order to refund a transaction, you need a transaction id.

```php
$transactionId = 'tran_54807f698f64d';
$resp = $pp->transactionRefund( $transactionId );
```

Response:
```json
{
    "data": {
        "transactionid": "tran_54807f698f64d"
    },
    "message": "Refund was successful.",
    "status": 200
}
```
