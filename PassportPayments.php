<?php

class PassportPayments {

	// define version
	const API_VERSION = '1.0.01';

	// define api base url
	const API_URL = 'https://sandbox.passportpayments.com';

	const STATUS_SUCCESS = 200;

	// client_id and client_secret provided by PassportPayements
	protected $client_id = null;
	protected $client_secret = null;
	protected $end_point = null;
	protected $public_key = null;

	// app_token given by the oauth authentication
	protected $access_token = null;

	// request methods
	const METHOD_GET = "GET";
    const METHOD_POST = "POST";
    const METHOD_DELETE = "DELETE";


	/**
		* Default constructor
		* @param string clientId
		* @param string clientSecret
	**/

	function __construct ( $clientId, $clientSecret, $publicKey, $endPoint = self::API_URL ){
		$this->setClientId ( $clientId );
		$this->setClientSecret ( $clientSecret );
		$this->setEndPoint ($endPoint);
		$this->setPublicKey ($publicKey);
	}


	/**
		* Initializing api app ID
		* @param string clientId for PassportPayments application
	*/

	private function setClientId ( $clientId ){
		$this->client_id = (string)$clientId;
	}

	/**
		* Initializing user app secret
		* @param string clientSecret for PassportPayments application
	*/

	private function setClientSecret ( $clientSecret ){
		$this->client_secret = (string)$clientSecret;
	}


	/**
		* Initializing user api Key
		* @param string appKey for PassportPayments application
	**/

	private function setEndPoint ( $endPoint ){
		$this->end_point = (string)$endPoint;
	}

	/**
		* Initializing user api Key
		* @param string appKey for PassportPayments application
	**/

	private function setPublicKey ( $publicKey ){
		$this->public_key = (string)$publicKey;
	}

	/**
		* Funtion to get the version number
	**/

	public function getVersin(){
		return API_VERSION;
	}

	/**
		* Function to get temporary card token
		* @param string cardnumber
        * @param string expmonth
        * @param string expyear
        * @param string cvv // optional
        * @param string nameoncard // optional
        * @return json customer details
	**/

	public function getCardToken( $cardnumber, $expmonth, $expyear, $cvv = "", $nameoncard = "" ) {

		$this->authenticate();

		$uri = "/cardtoken";
		$params['cardnumber'] = $cardnumber;
		$params['expmonth'] = $expmonth;
		$params['expyear'] = $expyear;
		$params['cvv'] = $cvv;
		$params['nameoncard'] = $nameoncard;
		$params['appkey'] = $this->public_key;

		$request = $this->requestResource(self::METHOD_GET, $uri, $params);
		return $request;
	}

	/**
		* Function to get pretransaction token
		* @param string cardtmptoken
        * @param string amount
        * @param string email
        * @return json
	**/

	public function getPreTransactionToken ( $cardtmptoken, $amount, $email = "", $extra_params=array() ) {

		$this->authenticate();

		$uri = "/getpretransactiontoken";
		$params['cardtmptoken'] = $cardtmptoken;
		$params['amount'] = $amount;
		$params['email'] = $email;
		$params['appkey'] = $this->public_key;
		$params = array_merge($params,$extra_params);

		$request = $this->requestResource(self::METHOD_GET, $uri, $params);
		return $request;
	}



	/**
		* Function to add customer
		* @param string firstname
        * @param string lastname // optional
        * @param string email
        * @param string phone // optional
        * @param string card_tmptoken
        * @return json customer details
	**/

	public function saveCustomer ($firstname, $lastname = '', $email, $phone = '', $card_tmptoken = ''){

		$this->authenticate();

		$uri = "/customers";

		$params['firstname'] = $firstname;
		$params['lastname'] = $lastname;
		$params['email'] = $email;
		$params['phone'] = $phone;
		$params['card_tmptoken'] = $card_tmptoken;

		$request = $this->requestResource(self::METHOD_POST, $uri, $params);

		return $request;
	}

	/**
		* Funtion to update a customer
		* @param string customerId
		* @param string updates [ firstname ]
        * @param string updates [ lastname ]
        * @param string updates [ phone ]
        * @return json customer details
	**/

	public function updateCustomer ( $customerId, $updates){
		$this->authenticate();
		$uri = "/customers/".$customerId;
		$request = $this->requestResource(self::METHOD_POST, $uri, $updates);
		return $request;
	}


	/**
		* Funtion to associate a card with a customer
		* @param string customerId
        * @param string card_tmptoken
        * @return string card_id
	**/

	public function addCard ( $customerId, $card_tmptoken){
		$this->authenticate();
		$uri = "/customers/".$customerId;
		$params['card_tmptoken'] = $card_tmptoken;
		$request = $this->requestResource(self::METHOD_POST, $uri, $params);
		return $request;
	}


	/**
		* Funtion to associate a card with a customer
		* @param string customerId
        * @param string card_id
        * @param string updates [ expmonth ]
        * @param string updates [ expyear ]
	**/

	public function updateCard ( $customerId, $card_id, $updates){
		$this->authenticate();
		$uri = "/customers/".$customerId."/cards/".$card_id;
		$request = $this->requestResource(self::METHOD_POST, $uri, $updates);
		return $request;
	}

	/**
		* Funtion to deassociate a card with a customer
		* @param string customerId
        * @param string cardId
        * @return json reponse
	**/

	public function deleteCard ( $customerId, $cardId ){
		$this->authenticate();
		$uri = "/customers/".$customerId."/cards/".$cardId;
		$request = $this->requestResource(self::METHOD_DELETE, $uri);
		return $request;
	}


	/**
		* Funtion to get all customer details
		* @return json all customer details
	**/

	public function getAllCustomers(){
		$this->authenticate();
		$uri = "/customers";
		$request = $this->requestResource(self::METHOD_GET, $uri);
		return $request;
	}

	/**
		* Function to get one customer details
		* @param string customerId
		* @return json customer details
	**/

	public function getCustomer( $customerId ){
		$this->authenticate();
		$uri = "/customers/".$customerId;
		$request = $this->requestResource(self::METHOD_GET, $uri);
		return $request;
	}

	/**
		* Function to get one customer details
		* @param string customerId
		* @return json delete message
	**/

	public function deleteCustomer( $customerId ){
		$this->authenticate();
		$uri = "/customers/".$customerId;
		$request = $this->requestResource(self::METHOD_DELETE, $uri);
		return $request;
	}

	/**
		* Function to charge a card by card identifier
		* @param string cardId - This is the cardid obtained from adding a card to with customer using card_tmptoken
		* @param string $params['amount']
		* @param string $params['product_id']
		* @param int $params['product_quantity']
		* @param string $params['currency'] options : USD / CAD
		* @return string transactionId - if the transaction is successfull
	**/

	public function captureByCardId( $cardId, $params = array() ){
		$this->authenticate();
		$uri = "/charges/capture/".$cardId;

		if( empty($params['amount']) ) $params['amount'] = 0;
		if( empty($params['product_id']) ) $params['product_id'] = 0;
		if( empty($params['product_quantity']) ) $params['product_quantity'] = 0;

		$response = $this->requestResource(self::METHOD_POST, $uri, $params);
		// if ($response->status == self::STATUS_SUCCESS){
		// 	//return $response->data->transactionid;
		// }
		return $response;
	}


	/**
		* Function to charge a card by card temporary token
		* @param string cardTmpToken - This is the card_tmptoken obtained during adding a new card
		* @param string $params['amount']
		* @param string $params['product_id']
		* @param int $params['product_quantity']
		* @param string $params['currency'] options : USD / CAD
		* @return string transactionId - if the transaction is successfull
	**/

	public function captureByCardTmpToken( $cardTmpToken, $params=array() ){
		$this->authenticate();
		$uri = "/charges/capture";

		if( empty($params['amount']) ) $params['amount'] = 0;
		if( empty($params['product_id']) ) $params['product_id'] = 0;
		if( empty($params['product_quantity']) ) $params['product_quantity'] = 0;

		$params['card_tmptoken'] = $cardTmpToken;
		$response = $this->requestResource(self::METHOD_POST, $uri, $params);
		if ($response->status == self::STATUS_SUCCESS){
			return $response->data->transactionid;
		}
		return $response;
	}

	/**
		* Function to get charge by pretransaction token
		* @param string preTransactionToken
		* @return json response
	**/


	public function captureByPreTransactionToken( $preTransactionToken ) {
		$this->authenticate();
		$uri = "/charges/capturebypretransactiontoken/".$preTransactionToken;
		$response = $this->requestResource(self::METHOD_POST, $uri);
		return $response;
	}



	/**
		* Function to void a transaction
		* @param string transactionId
		* @return string transactionId - if the transaction is successfull
	**/

	public function transactionVoid( $transactionId ){
		$this->authenticate();
		$uri = "/charges/void/".$transactionId;

		$response = $this->requestResource(self::METHOD_POST, $uri);
		return $response;
	}

	/**
		* Function to refund a transaction
		* @param string transactionId
		* @param int amount // optional
		* @return string transactionId - if the transaction is successfull
	**/

	public function transactionRefund( $transactionId, $amount = 0 ){
		$this->authenticate();
		if (!$amount){
			$uri = "/charges/refund/".$transactionId;
		} else {
			$uri = "/charges/refund/".$transactionId."/".$amount;
		}

		$response = $this->requestResource(self::METHOD_POST, $uri);
		return $response;
	}

	/**
		* Function to retrive last 1000 transaction information
	**/
	public function getAllTransactions( $customerId = null ){
		$this->authenticate();

		if( $customerId )
			$uri = "/charges/".$customerId;
		else
			$uri = "/charges";

		$response = $this->requestResource(self::METHOD_GET, $uri);
		if ($response->status == self::STATUS_SUCCESS){
			return $response->data;
		}
		return $response;
	}

	/**
		* Function to get a transaction details
	**/

	public function getTransaction( $transactionId ) {
		$this->authenticate();
		$uri = "/charge/".$transactionId;
		$response = $this->requestResource(self::METHOD_GET, $uri);
		return $response;
	}

	/**
		* Function to get all / one product details
		* @param $productId // optional
		* @return product details in json
	**/

	public function getProduct( $productId = '' ){
		$this->authenticate();
		$productId = (string) $productId;
		if(empty($productId))
			$uri = "/product";
		else
			$uri = "/product/".$productId;

		$response = $this->requestResource(self::METHOD_GET, $uri);

		return $response;
	}

	/**
		* Function to add wallet cash
		* @param $customer_id
		* @param $amount_in_cents
		* @return response
	**/

	public function addWalletCash( $customer_id, $amount_in_cents ) {
		$this->authenticate();
		$uri = "/wallet/addcash/".$customer_id."/".$amount_in_cents;
		$response = $this->requestResource(self::METHOD_POST, $uri);
		return $response;
	}

	/**
		* Function to charge wallet cash
		* @param $customer_id
		* @param $amount_in_cents
		* @return response
	**/

	public function chargeWalletCash( $customer_id, $amount_in_cents ) {
		$this->authenticate();
		$uri = "/wallet/chargecash/".$customer_id."/".$amount_in_cents;
		$response = $this->requestResource(self::METHOD_POST, $uri);
		return $response;
	}

	/**
		* Function to get wallet balance
		* @param $customer_id
		* @return response
	**/

	public function getWalletBalance( $customer_id ) {
		$this->authenticate();
		$uri = "/wallet/balance/".$customer_id;
		$response = $this->requestResource(self::METHOD_GET, $uri);
		return $response;
	}

	/**
		* Function to get wallet balance
		* @param $customer_id
		* @return response
	**/

	public function refundTransaction( $transaction_id, $amount_in_cents = 0 ) {
		$this->authenticate();
		if ($amount_in_cents) {
			$uri = "/wallet/refund/".$transaction_id."/".$amount_in_cents;
		} else {
			$uri = "/wallet/refund/".$transaction_id;
		}
		$response = $this->requestResource(self::METHOD_POST, $uri);
		return $response;
	}

	/**
		* Funtion to get authentication token from oauth
	**/

	private function authenticate() {
		$uri = "/oauth/accesstoken";
        $params = array(
			            'client_id' => $this->client_id,
			            'client_secret' => $this->client_secret,
			            'grant_type' => 'client_credentials'
			        	);

        $response = $this->requestResource(self::METHOD_POST, $uri, $params);

        if(!empty($response) && $response->status == self::STATUS_SUCCESS){
            $this->access_token = $response->data->access_token;
        }
	}

	/**
		* Function to send the request
	**/

	public function requestResource ( $method, $apiUri, $params=array() ){
	    $accessToken = $this->access_token;

	    $apiUrl = $this->end_point . $apiUri;

	    if(!empty($accessToken)){
        	$tmpparams['access_token'] = $accessToken;
        	$p = json_encode($params);
        	$tmpparams['phpcrypt'] = $this->cryptoJsAesEncrypt($this->client_secret, $p);
        	$params = $tmpparams;
        }

        $params_uri = '';
        $curlOptions = array();

        switch($method){

            case self::METHOD_GET:
                foreach ($params as $key => $value) {
                    $params_uri .= $key . '=' . urlencode($value) . '&';
                }
                $apiUrl = $apiUrl . '?' . rtrim($params_uri, "&");
                break;

            case self::METHOD_POST:
                $curlOptions[CURLOPT_POST] = true;
                $curlOptions[CURLOPT_POSTFIELDS] = http_build_query($params);
                break;

            case self::METHOD_DELETE:
                foreach ($params as $key => $value) {
                    $params_uri .= $key . '=' . urlencode($value) . '&';
                }
                $apiUrl = $apiUrl . '?' . rtrim($params_uri, "&");
                $curlOptions[CURLOPT_CUSTOMREQUEST] = 'DELETE';
                break;
        }

        $curlOptions[CURLOPT_URL] = $apiUrl;
        $curlOptions[CURLOPT_RETURNTRANSFER] = true;
        $curlOptions[CURLOPT_SSL_VERIFYPEER] = false;
        $curlOptions[CURLOPT_SSL_VERIFYHOST] = false;


        $curl = curl_init();
        $setopt = curl_setopt_array($curl, $curlOptions);
        $auth = curl_exec($curl);
        $response = json_decode($auth);
        return $response;
	}

	private function cryptoJsAesEncrypt($passphrase, $value){
	    $salt = openssl_random_pseudo_bytes(8);
	    $salted = '';
	    $dx = '';
	    while (strlen($salted) < 48) {
	        $dx = md5($dx.$passphrase.$salt, true);
	        $salted .= $dx;
	    }
	    $key = substr($salted, 0, 32);
	    $iv  = substr($salted, 32,16);
	    $encrypted_data = openssl_encrypt(json_encode($value), 'aes-256-cbc', $key, true, $iv);
	    $data = array("ct" => base64_encode($encrypted_data), "iv" => bin2hex($iv), "s" => bin2hex($salt));
	    return json_encode($data);
	}
}







