<?php 

class PassportPayments {

	// define version
	const API_VERSION = '1.0.0';

	// define api base url
	const API_URL = 'https://sandbox.passportpayments.com';

	const STATUS_SUCCESS = 200;

	// app_id and app_secret provided by PassportPayements
	protected $app_id = null;
	protected $app_secret = null;
	protected $end_point = null;

	// app_token given by the oauth authentication
	protected $access_token = null;

	// request methods
	const METHOD_GET = "GET";
    const METHOD_POST = "POST";
    const METHOD_DELETE = "DELETE";


	/**
		* Default constructor
		* @param string appId 
		* @param string appSecret
	**/

	function __construct ( $appId, $appSecret, $endPoint = self::API_URL ){
		$this->setAppId ( $appId );
		$this->setAppSecret ( $appSecret );
		$this->setEndPoint ($endPoint);
	}


	/**
		* Initializing api app ID
		* @param string appId for PassportPayments application
	*/

	private function setAppId ( $appId ){
		$this->app_id = (string)$appId;
	}

	/**
		* Initializing user app secret
		* @param string appSecret for PassportPayments application
	*/

	private function setAppSecret ( $appSecret ){
		$this->app_secret = (string)$appSecret;
	}

	
	/**
		* Initializing user api Key
		* @param string appKey for PassportPayments application
	**/

	private function setEndPoint ( $endPoint ){
		$this->end_point = (string)$endPoint;
	}

	/**
		* Funtion to get the version number
	**/

	public function getVersin(){
		return API_VERSION;
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
		* @param string amountInCents
		* @param string productId
		* @param string productQuantity
		* @return string transactionId - if the transaction is successfull 
	**/

	public function captureByCardId( $cardId, $amountInCents = 0, $productId = 0, $productQuantity = 0 ){
		$this->authenticate();
		$uri = "/charges/capture/".$cardId;

		$params = array();
		if( !empty($amountInCents) ) $params['amount'] = $amountInCents;
		if( !empty($productId) ) $params['product_id'] = $productId;
		if( !empty($productQuantity) ) $params['product_quantity'] = $productQuantity;

		$response = $this->requestResource(self::METHOD_POST, $uri, $params);
		if ($response->status == self::STATUS_SUCCESS){
			return $response->data->transactionid;
		}
		return $response;
	}


	/**
		* Function to charge a card by card temporary token
		* @param string cardTmpToken - This is the card_tmptoken obtained during adding a new card
		* @param string amountInCents
		* @param string productId
		* @param string productQuantity
		* @return string transactionId - if the transaction is successfull 
	**/

	public function captureByCardTmpToken( $cardTmpToken, $amountInCents = 0, $productId = 0, $productQuantity = 0 ){
		$this->authenticate();
		$uri = "/charges/capture";

		$params = array();

		if( !empty($amountInCents) ) $params['amount'] = $amountInCents;
		if( !empty($productId) ) $params['product_id'] = $productId;
		if( !empty($productQuantity) ) $params['product_quantity'] = $productQuantity;
		
		$params['card_tmptoken'] = $cardTmpToken;

		$response = $this->requestResource(self::METHOD_POST, $uri, $params);
		if ($response->status == self::STATUS_SUCCESS){
			return $response->data->transactionid;
		}
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
		if ($response->status == self::STATUS_SUCCESS){
			return $response->data->transactionid;
		}
		return $response;
	}

	/**
		* Function to refund a transaction
		* @param string transactionId
		* @return string transactionId - if the transaction is successfull 
	**/

	public function transactionRefund( $transactionId ){
		$this->authenticate();
		$uri = "/charges/refund/".$transactionId;

		$response = $this->requestResource(self::METHOD_POST, $uri);
		if ($response->status == self::STATUS_SUCCESS){
			return $response->data->transactionid;
		}
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
		* Function to add a new product
		* @param string productName  //mandatory parameter
    	* @param string rateInCents //mandatory parameter
    	* @return string productId 
	**/

	public function addProduct($product){
		$this->authenticate();
		$uri = "/product/add";
		$response = $this->requestResource(self::METHOD_POST, $uri, $product);
		return $response;
	}


	/**
		* Function to update an existing product
		* @param string productId  //mandatory parameter
		* @param string productName  
    	* @param string rateInCents 
    	* @return string success message 
	**/

	public function updateProduct($productId, $product){
		$this->authenticate();
		$uri = "/product/".$productId;
		$response = $this->requestResource(self::METHOD_POST, $uri, $product);
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
		* Function to get all / one product details
		* @param $productId
		* @return success message
	**/

	public function deleteProduct( $productId ){
		$this->authenticate();
		$productId = (string) $productId;
		$uri = "/product/".$productId;

		$response = $this->requestResource(self::METHOD_DELETE, $uri);

		return $response;
	}



	/**
		* Funtion to get authentication token from oauth
	**/

	private function authenticate() {
		$uri = "/oauth/accesstoken";
        $params = array(
			            'client_id' => $this->app_id,
			            'client_secret' => $this->app_secret,
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
        	$params['access_token'] = $accessToken;
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

        error_log($apiUrl);
        error_log(json_encode($params));
        $curlOptions[CURLOPT_URL] = $apiUrl;
        $curlOptions[CURLOPT_RETURNTRANSFER] = true;
        $curlOptions[CURLOPT_SSL_VERIFYPEER] = false;
        $curlOptions[CURLOPT_SSL_VERIFYHOST] = false;

        
        $curl = curl_init();
        $setopt = curl_setopt_array($curl, $curlOptions);
        $auth = curl_exec($curl);
        $response = json_decode($auth);
        error_log(json_encode($response));
        return $response;
	}
}







