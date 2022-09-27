<?
require 'access.php';

class AMO{
	private static $access_token;
	private static $subdomain;
	private $headers;
	private $code;

	public function __construct(){
		$this->headers = [
			'Content-Type: application/json',
	    	'Authorization: Bearer ' . self::$access_token,
		];
	}

	public function getContact($phone, $page){
		$method = "/api/v4/contacts";

		$get = [
			"with" => "catalog_elements,leads,customers",
			"page" => $page,
			"limit" => 250,
			"order" => [
				"id"=>"asc"
			],	
		];

		$link = "https://".self::$subdomain.".amocrm.ru".$method."?".http_build_query($get);

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_USERAGENT, 'amoCRM-API-client/1.0');
		curl_setopt($curl, CURLOPT_URL, $link);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $this->headers);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($curl, CURLOPT_HEADER, false);
		$out = curl_exec($curl);
		$this->code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		$this->code = (int) $this->code;
		$errors = [
		    301 => 'Moved permanently.',
		    400 => 'Wrong structure of the array of transmitted data, or invalid identifiers of custom fields.',
		    401 => 'Not Authorized. There is no account information on the server. You need to make a request to another server on the transmitted IP.',
		    403 => 'The account is blocked, for repeatedly exceeding the number of requests per second.',
		    404 => 'Not found.',
		    500 => 'Internal server error.',
		    502 => 'Bad gateway.',
		    503 => 'Service unavailable.'
		];

		if ($this->code < 200 || $this->code > 204) die( "Error ".$this->code.". " . (isset($errors[$this->code]) ? $errors[$this->code] : 'Undefined error') );
		$response = json_decode($out, true);
		curl_close($curl);

		return $response;
	}

	public function setContact($name, $phone, $email){
		$method = "/api/v4/contacts";

		$data = [
			[
				"name" => $name." (Тестовый аккаунт c razvit.fixmaski.ru)",
				"custom_fields_values" => [
					[
						"field_code" => "PHONE",
						"values" => [
							[
								"value"=> $phone,
								"enum_code"=>"MOB"
							]
						]
					],
					[
						"field_code" => "EMAIL",
						"values" => [
							[
								"value"=> $email,
								"enum_code"=>"PRIV"
							]
						]
					]
				]
			]
		];

		$link = "https://".self::$subdomain.".amocrm.ru".$method;

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_USERAGENT, 'amoCRM-API-client/1.0');
		curl_setopt($curl, CURLOPT_URL, $link);
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
		curl_setopt($curl, CURLOPT_HTTPHEADER, $this->headers);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($curl, CURLOPT_HEADER, false);
		$out = curl_exec($curl);
		$this->code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		$this->code = (int) $this->code;
		$errors = [
		    301 => 'Moved permanently.',
		    400 => 'Wrong structure of the array of transmitted data, or invalid identifiers of custom fields.',
		    401 => 'Not Authorized. There is no account information on the server. You need to make a request to another server on the transmitted IP.',
		    403 => 'The account is blocked, for repeatedly exceeding the number of requests per second.',
		    404 => 'Not found.',
		    500 => 'Internal server error.',
		    502 => 'Bad gateway.',
		    503 => 'Service unavailable.'
		];

		if ($this->code < 200 || $this->code > 204) die( "Error ".$this->code.". " . (isset($errors[$this->code]) ? $errors[$this->code] : 'Undefined error') );
		$response = json_decode($out, true);
		curl_close($curl);	

		return $response;
	}

	public function setConsultation($order, $name = "Тестовый запрос на консультацию c razvit.fixmaski.ru"){
		$method = "/api/v4/leads";

		$data = [
			[
				"name" => $name,
				"status_id" => 20924776,
				"pipeline_id" => 749086,
				"_embedded" => [
					"tags"=>[
						[
							"id"=> 473939
						]
					],
					"contacts" => [
						[
							"id"=>(int)$order["contact_id"],
							"is_main"=>true
						]
					]
				]
			]
		];

		$link = "https://".self::$subdomain.".amocrm.ru".$method;

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_USERAGENT, 'amoCRM-API-client/1.0');
		curl_setopt($curl, CURLOPT_URL, $link);
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
		curl_setopt($curl, CURLOPT_HTTPHEADER, $this->headers);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($curl, CURLOPT_HEADER, false);
		$out = curl_exec($curl);
		$this->code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		$this->code = (int) $this->code;
		$errors = [
		    301 => 'Moved permanently.',
		    400 => 'Wrong structure of the array of transmitted data, or invalid identifiers of custom fields.',
		    401 => 'Not Authorized. There is no account information on the server. You need to make a request to another server on the transmitted IP.',
		    403 => 'The account is blocked, for repeatedly exceeding the number of requests per second.',
		    404 => 'Not found.',
		    500 => 'Internal server error.',
		    502 => 'Bad gateway.',
		    503 => 'Service unavailable.'
		];

		//if ($code < 200 || $code > 204) die( "Error $code. " . (isset($errors[$code]) ? $errors[$code] : 'Undefined error') );
		$response = json_decode($out, true);
		curl_close($curl);

		return $response;
	}

	public function setComplexConsultation($contact, $name = "Тестовый запрос на консультацию c razvit.fixmaski.ru"){
		$method = "/api/v4/leads/complex";

		$data = [
			[
				"name" => $name,
				"status_id" => 20924776,
				"pipeline_id" => 749086,
				"_embedded" => [
					"tags"=>[
						[
							"id"=> 473939
						]
					],
					"contacts" => [
						[
							"name"=> $contact["name"]." (Тестовый аккаунт c razvit.fixmaski.ru)",
							"custom_fields_values" => [
								[
									"field_code" => "PHONE",
									"values" => [
										[
											"value"=>$contact["phone"],
											"enum_code"=>"MOB"
										]
									]
								]
							]
						]
					]
				]
			]
		];

		$link = "https://".self::$subdomain.".amocrm.ru".$method;

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_USERAGENT, 'amoCRM-API-client/1.0');
		curl_setopt($curl, CURLOPT_URL, $link);
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
		curl_setopt($curl, CURLOPT_HTTPHEADER, $this->headers);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($curl, CURLOPT_HEADER, false);
		$out = curl_exec($curl);
		$this->code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		$this->code = (int) $this->code;
		$errors = [
		    301 => 'Moved permanently.',
		    400 => 'Wrong structure of the array of transmitted data, or invalid identifiers of custom fields.',
		    401 => 'Not Authorized. There is no account information on the server. You need to make a request to another server on the transmitted IP.',
		    403 => 'The account is blocked, for repeatedly exceeding the number of requests per second.',
		    404 => 'Not found.',
		    500 => 'Internal server error.',
		    502 => 'Bad gateway.',
		    503 => 'Service unavailable.'
		];

		//if ($code < 200 || $code > 204) die( "Error $code. " . (isset($errors[$code]) ? $errors[$code] : 'Undefined error') );
		$response = json_decode($out, true);
		curl_close($curl);

		return $response;
	}

	public function setLead($order){
		$method = "/api/v4/leads";

		$data = [
			[
				"name" => "Тестовая заявка c razvit.fixmaski.ru",
				"status_id" => 20924776,
				"pipeline_id" => 749086,
				"custom_fields_values"=>[
					[
						"field_id" => 468425,
						"values" => [
							[
								"value"=>(string)$order["price"]
							]
						] 
					]
				],
				"_embedded" => [
					"tags"=>[
						[
							"id"=> 473939
						]
					],
					"contacts" => [
						[
							"id"=>(int)$order["contact_id"],
							"is_main"=>true
						]
					]
				]
			]
		];

		$link = "https://".self::$subdomain.".amocrm.ru".$method;

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_USERAGENT, 'amoCRM-API-client/1.0');
		curl_setopt($curl, CURLOPT_URL, $link);
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
		curl_setopt($curl, CURLOPT_HTTPHEADER, $this->headers);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($curl, CURLOPT_HEADER, false);
		$out = curl_exec($curl);
		$this->code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		$this->code = (int) $this->code;
		$errors = [
		    301 => 'Moved permanently.',
		    400 => 'Wrong structure of the array of transmitted data, or invalid identifiers of custom fields.',
		    401 => 'Not Authorized. There is no account information on the server. You need to make a request to another server on the transmitted IP.',
		    403 => 'The account is blocked, for repeatedly exceeding the number of requests per second.',
		    404 => 'Not found.',
		    500 => 'Internal server error.',
		    502 => 'Bad gateway.',
		    503 => 'Service unavailable.'
		];

		//if ($code < 200 || $code > 204) die( "Error $code. " . (isset($errors[$code]) ? $errors[$code] : 'Undefined error') );
		$response = json_decode($out, true);
		curl_close($curl);

		return $response;
	}

	public function setComplexLead($order, $contact){
		$method = "/api/v4/leads/complex";

		$data = [
			[
				"name" => "Тестовая заявка c razvit.fixmaski.ru",
				"status_id" => 20924776,
				"pipeline_id" => 749086,
				"custom_fields_values"=>[
					[
						"field_id" => 468425,
						"values" => [
							[
								"value"=>(string)$order["price"]
							]
						] 
					]
				],
				"_embedded" => [
					"tags"=>[
						[
							"id"=> 473939
						]
					],
					"contacts" => [
						[
							"name"=> $contact["name"]." (Тестовый аккаунт c razvit.fixmaski.ru)",
							"custom_fields_values" => [
								[
									"field_code" => "PHONE",
									"values" => [
										[
											"value"=>$contact["phone"],
											"enum_code"=>"MOB"
										]
									]
								],
								[
									"field_code"=>"EMAIL",
									"values" => [
										[
											"value" => $contact["email"],
											"enum_code"=>"PRIV"
										]
									]
								]
							]
						]
					]
				]
			]
		];

		$link = "https://".self::$subdomain.".amocrm.ru".$method;

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_USERAGENT, 'amoCRM-API-client/1.0');
		curl_setopt($curl, CURLOPT_URL, $link);
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
		curl_setopt($curl, CURLOPT_HTTPHEADER, $this->headers);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($curl, CURLOPT_HEADER, false);
		$out = curl_exec($curl);
		$this->code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		$this->code = (int) $this->code;
		$errors = [
		    301 => 'Moved permanently.',
		    400 => 'Wrong structure of the array of transmitted data, or invalid identifiers of custom fields.',
		    401 => 'Not Authorized. There is no account information on the server. You need to make a request to another server on the transmitted IP.',
		    403 => 'The account is blocked, for repeatedly exceeding the number of requests per second.',
		    404 => 'Not found.',
		    500 => 'Internal server error.',
		    502 => 'Bad gateway.',
		    503 => 'Service unavailable.'
		];

		//if ($code < 200 || $code > 204) die( "Error $code. " . (isset($errors[$code]) ? $errors[$code] : 'Undefined error') );
		$response = json_decode($out, true);
		curl_close($curl);

		return $response;	
	}

	public function getFields(){
		$method = "/api/v4/leads/custom_fields";

		/*$get = array(
			"with" => "catalog_elements,contacts,source_id",
			"page" => 1,
			"limit" => 50,
			"order" => array(
				"id"=>"asc"
			)	
		);*/

		$get = [
			"page" => 1
		];

		$link = "https://".self::$subdomain.".amocrm.ru".$method."?".http_build_query($get);

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_USERAGENT, 'amoCRM-API-client/1.0');
		curl_setopt($curl, CURLOPT_URL, $link);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $this->headers);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($curl, CURLOPT_HEADER, false);
		$out = curl_exec($curl);
		$this->code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		$this->code = (int) $this->code;
		$errors = [
		    301 => 'Moved permanently.',
		    400 => 'Wrong structure of the array of transmitted data, or invalid identifiers of custom fields.',
		    401 => 'Not Authorized. There is no account information on the server. You need to make a request to another server on the transmitted IP.',
		    403 => 'The account is blocked, for repeatedly exceeding the number of requests per second.',
		    404 => 'Not found.',
		    500 => 'Internal server error.',
		    502 => 'Bad gateway.',
		    503 => 'Service unavailable.'
		];

		if ($this->code < 200 || $this->code > 204) die( $out);//"Error $code. " . (isset($errors[$code]) ? $errors[$code] : 'Undefined error') );
		$response = json_decode($out, true);
		curl_close($curl);	

		return $response;
	}

	public function getLeadTags(){
		$method = "/api/v4/leads/tags";

		$get = array(
			"page" => 1,
			"limit" => 250
		);
		$link = "https://".self::$subdomain.".amocrm.ru".$method."?".http_build_query($get);

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_USERAGENT, 'amoCRM-API-client/1.0');
		curl_setopt($curl, CURLOPT_URL, $link);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $this->headers);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($curl, CURLOPT_HEADER, false);
		$out = curl_exec($curl);
		$this->code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		$this->code = (int) $this->code;
		$errors = [
		    301 => 'Moved permanently.',
		    400 => 'Wrong structure of the array of transmitted data, or invalid identifiers of custom fields.',
		    401 => 'Not Authorized. There is no account information on the server. You need to make a request to another server on the transmitted IP.',
		    403 => 'The account is blocked, for repeatedly exceeding the number of requests per second.',
		    404 => 'Not found.',
		    500 => 'Internal server error.',
		    502 => 'Bad gateway.',
		    503 => 'Service unavailable.'
		];

		if ($this->code < 200 || $this->code > 204) die( "Error $this->code. " . (isset($errors[$this->code]) ? $errors[$this->code] : 'Undefined error') );
		$response = json_decode($out, true);
		curl_close($curl);	

		return $response;
	}

	public function setNote($id, $order){
		$id = (int)$id;
		$method = "/api/v4/leads/{$id}/notes";
		$data = [
			[
				"note_type" => "common",
				"params" => [
					"text" => $order["description"]
				]
			]
		];

		$link = "https://".self::$subdomain.".amocrm.ru".$method;

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_USERAGENT, 'amoCRM-API-client/1.0');
		curl_setopt($curl, CURLOPT_URL, $link);
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
		curl_setopt($curl, CURLOPT_HTTPHEADER, $this->headers);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($curl, CURLOPT_HEADER, false);
		$out = curl_exec($curl);
		$this->code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		$this->code = (int) $this->code;
		$errors = [
		    301 => 'Moved permanently.',
		    400 => 'Wrong structure of the array of transmitted data, or invalid identifiers of custom fields.',
		    401 => 'Not Authorized. There is no account information on the server. You need to make a request to another server on the transmitted IP.',
		    403 => 'The account is blocked, for repeatedly exceeding the number of requests per second.',
		    404 => 'Not found.',
		    500 => 'Internal server error.',
		    502 => 'Bad gateway.',
		    503 => 'Service unavailable.'
		];
		$fp = fopen("text.log", "w");
		fwrite($fp, $out);
		fclose($fp);
		if ($this->code < 200 || $this->code > 204) die( $out );
		$response = json_decode($out, true);
		curl_close($curl);	

		return $response;
	}

	public function getCode(){
		return $this->code;
	}

	public static function setAccessToken($token){
		self::$access_token = $token;
	}

	public static function setSubdomain($subdomain){
		self::$subdomain = $subdomain;
	}
}

AMO::setAccessToken($access_token);
AMO::setSubdomain($subdomain);
//AMO::access_token = $access_token;

/*function getAccount(){
	$method = "/api/v4/account";

	$headers = [
	    'Content-Type: application/json',
	    'Authorization: Bearer ' . $access_token,
	];

	$link = "https://$subdomain.amocrm.ru".$method;

	$curl = curl_init();
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_USERAGENT, 'amoCRM-API-client/1.0');
	curl_setopt($curl, CURLOPT_URL, $link);
	curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($curl, CURLOPT_COOKIEFILE, 'amo/cookie.txt');
	curl_setopt($curl, CURLOPT_COOKIEJAR, 'amo/cookie.txt');
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($curl, CURLOPT_HEADER, false);
	$out = curl_exec($curl);
	$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
	$code = (int) $code;
	$errors = [
	    301 => 'Moved permanently.',
	    400 => 'Wrong structure of the array of transmitted data, or invalid identifiers of custom fields.',
	    401 => 'Not Authorized. There is no account information on the server. You need to make a request to another server on the transmitted IP.',
	    403 => 'The account is blocked, for repeatedly exceeding the number of requests per second.',
	    404 => 'Not found.',
	    500 => 'Internal server error.',
	    502 => 'Bad gateway.',
	    503 => 'Service unavailable.'
	];

	if ($code < 200 || $code > 204) die( "Error $code. " . (isset($errors[$code]) ? $errors[$code] : 'Undefined error') );
	$response = json_decode($out, true);
	curl_close($curl);

	return $response;
}

function getLeads(){
	$method = "/api/v4/leads";

	$get = array(
		"with" => "catalog_elements,contacts,source_id",
		"page" => 1,
		"limit" => 50,
		"order" => array(
			"id"=>"asc"
		)	
	);

	$headers = [
	    'Content-Type: application/json',
	    'Authorization: Bearer ' . $access_token,
	];

	$link = "https://$subdomain.amocrm.ru".$method."?".http_build_query($get);

	$curl = curl_init();
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_USERAGENT, 'amoCRM-API-client/1.0');
	curl_setopt($curl, CURLOPT_URL, $link);
	curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($curl, CURLOPT_HEADER, false);
	$out = curl_exec($curl);
	$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
	$code = (int) $code;
	$errors = [
	    301 => 'Moved permanently.',
	    400 => 'Wrong structure of the array of transmitted data, or invalid identifiers of custom fields.',
	    401 => 'Not Authorized. There is no account information on the server. You need to make a request to another server on the transmitted IP.',
	    403 => 'The account is blocked, for repeatedly exceeding the number of requests per second.',
	    404 => 'Not found.',
	    500 => 'Internal server error.',
	    502 => 'Bad gateway.',
	    503 => 'Service unavailable.'
	];

	if ($code < 200 || $code > 204) die( "Error $code. " . (isset($errors[$code]) ? $errors[$code] : 'Undefined error') );
	$response = json_decode($out, true);
	curl_close($curl);	

	return $response;
}

function getUnsorted(){
	$method = "/api/v4/leads/unsorted";

	$get = array(
		"page" => 1,
		"limit" => 50,
		"order" => array(
			"id"=>"asc"
		)	
	);

	$headers = [
	    'Content-Type: application/json',
	    'Authorization: Bearer ' . $access_token,
	];

	$link = "https://$subdomain.amocrm.ru".$method."?".http_build_query($get);

	$curl = curl_init();
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_USERAGENT, 'amoCRM-API-client/1.0');
	curl_setopt($curl, CURLOPT_URL, $link);
	curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($curl, CURLOPT_HEADER, false);
	$out = curl_exec($curl);
	$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
	$code = (int) $code;
	$errors = [
	    301 => 'Moved permanently.',
	    400 => 'Wrong structure of the array of transmitted data, or invalid identifiers of custom fields.',
	    401 => 'Not Authorized. There is no account information on the server. You need to make a request to another server on the transmitted IP.',
	    403 => 'The account is blocked, for repeatedly exceeding the number of requests per second.',
	    404 => 'Not found.',
	    500 => 'Internal server error.',
	    502 => 'Bad gateway.',
	    503 => 'Service unavailable.'
	];

	if ($code < 200 || $code > 204) die( "Error $code. " . (isset($errors[$code]) ? $errors[$code] : 'Undefined error') );
	$response = json_decode($out, true);
	curl_close($curl);

	return $response;	
}

function getPipelines(){
	$method = "/api/v4/leads/pipelines";

	$headers = [
	    'Content-Type: application/json',
	    'Authorization: Bearer ' . $access_token,
	];

	$link = "https://$subdomain.amocrm.ru".$method;

	$curl = curl_init();
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_USERAGENT, 'amoCRM-API-client/1.0');
	curl_setopt($curl, CURLOPT_URL, $link);
	curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($curl, CURLOPT_HEADER, false);
	$out = curl_exec($curl);
	$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
	$code = (int) $code;
	$errors = [
	    301 => 'Moved permanently.',
	    400 => 'Wrong structure of the array of transmitted data, or invalid identifiers of custom fields.',
	    401 => 'Not Authorized. There is no account information on the server. You need to make a request to another server on the transmitted IP.',
	    403 => 'The account is blocked, for repeatedly exceeding the number of requests per second.',
	    404 => 'Not found.',
	    500 => 'Internal server error.',
	    502 => 'Bad gateway.',
	    503 => 'Service unavailable.'
	];

	if ($code < 200 || $code > 204) die( "Error $code. " . (isset($errors[$code]) ? $errors[$code] : 'Undefined error') );
	$response = json_decode($out, true);
	curl_close($curl);

	return $response;	
}

function getContacts(){
	$method = "/api/v4/contacts";

	$get = array(
		"with" => "catalog_elements,leads,customers",
		"page" => 1,
		"limit" => 50,
		"order" => array(
			"id"=>"asc"
		)	
	);

	$headers = [
	    'Content-Type: application/json',
	    'Authorization: Bearer ' . $access_token,
	];

	$link = "https://$subdomain.amocrm.ru".$method."?".http_build_query($get);

	$curl = curl_init();
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_USERAGENT, 'amoCRM-API-client/1.0');
	curl_setopt($curl, CURLOPT_URL, $link);
	curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($curl, CURLOPT_HEADER, false);
	$out = curl_exec($curl);
	$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
	$code = (int) $code;
	$errors = [
	    301 => 'Moved permanently.',
	    400 => 'Wrong structure of the array of transmitted data, or invalid identifiers of custom fields.',
	    401 => 'Not Authorized. There is no account information on the server. You need to make a request to another server on the transmitted IP.',
	    403 => 'The account is blocked, for repeatedly exceeding the number of requests per second.',
	    404 => 'Not found.',
	    500 => 'Internal server error.',
	    502 => 'Bad gateway.',
	    503 => 'Service unavailable.'
	];

	if ($code < 200 || $code > 204) die( "Error $code. " . (isset($errors[$code]) ? $errors[$code] : 'Undefined error') );
	$response = json_decode($out, true);
	curl_close($curl);

	return $response;
}

function setContacts(){
	$method = "/api/v4/contacts";

	$data = [
		[
			"name" => "Тестовый аккаунт c razvit.fixmaski.ru",
			"custom_fields_values" => [
				[
					"field_code" => "PHONE",
					"values" => [
						[
							"value"=>"+37377730378",
							"enum_code"=>"MOB"
						]
					]
				],
				[
					"field_code" => "EMAIL",
					"values" => [
						[
							"value"=>"ma-ks2@ya.ru",
							"enum_code"=>"PRIV"
						]
					]
				]
			]
		]
	];

	$headers = [
	    'Content-Type: application/json',
	    'Authorization: Bearer ' . $access_token,
	];

	$link = "https://$subdomain.amocrm.ru".$method;

	$curl = curl_init();
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_USERAGENT, 'amoCRM-API-client/1.0');
	curl_setopt($curl, CURLOPT_URL, $link);
	curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
	curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
	curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($curl, CURLOPT_HEADER, false);
	$out = curl_exec($curl);
	$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
	$code = (int) $code;
	$errors = [
	    301 => 'Moved permanently.',
	    400 => 'Wrong structure of the array of transmitted data, or invalid identifiers of custom fields.',
	    401 => 'Not Authorized. There is no account information on the server. You need to make a request to another server on the transmitted IP.',
	    403 => 'The account is blocked, for repeatedly exceeding the number of requests per second.',
	    404 => 'Not found.',
	    500 => 'Internal server error.',
	    502 => 'Bad gateway.',
	    503 => 'Service unavailable.'
	];

	if ($code < 200 || $code > 204) die( "Error $code. " . (isset($errors[$code]) ? $errors[$code] : 'Undefined error') );
	$response = json_decode($out, true);
	curl_close($curl);	

	return $response;
}

function getTags(){
	$method = "/api/v4/leads/tags";

	$get = array(
		"page" => 1,
		"limit" => 50
	);

	$headers = [
	    'Content-Type: application/json',
	    'Authorization: Bearer ' . $access_token,
	];

	$link = "https://$subdomain.amocrm.ru".$method."?".http_build_query($get);

	$curl = curl_init();
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_USERAGENT, 'amoCRM-API-client/1.0');
	curl_setopt($curl, CURLOPT_URL, $link);
	curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($curl, CURLOPT_HEADER, false);
	$out = curl_exec($curl);
	$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
	$code = (int) $code;
	$errors = [
	    301 => 'Moved permanently.',
	    400 => 'Wrong structure of the array of transmitted data, or invalid identifiers of custom fields.',
	    401 => 'Not Authorized. There is no account information on the server. You need to make a request to another server on the transmitted IP.',
	    403 => 'The account is blocked, for repeatedly exceeding the number of requests per second.',
	    404 => 'Not found.',
	    500 => 'Internal server error.',
	    502 => 'Bad gateway.',
	    503 => 'Service unavailable.'
	];

	if ($code < 200 || $code > 204) die( "Error $code. " . (isset($errors[$code]) ? $errors[$code] : 'Undefined error') );
	$response = json_decode($out, true);
	curl_close($curl);	

	return $response;	
}

function setLead(){
	$method = "/api/v4/leads";

	$data = [
		[
			"name" => "Тестовая сделка c razvit.fixmaski.ru",
			"price" => 9999999,
			"status_id" => 20924776,
			"pipeline_id" => 749086,
			"_embedded" => [
				"contacts" => [
					[
						"id"=>48385849,
						"is_main"=>true
					]
				]
			]
		]
	];

	$headers = [
	    'Content-Type: application/json',
	    'Authorization: Bearer ' . $access_token,
	];

	$link = "https://$subdomain.amocrm.ru".$method;

	$curl = curl_init();
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_USERAGENT, 'amoCRM-API-client/1.0');
	curl_setopt($curl, CURLOPT_URL, $link);
	curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
	curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
	curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($curl, CURLOPT_HEADER, false);
	$out = curl_exec($curl);
	$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
	$code = (int) $code;
	$errors = [
	    301 => 'Moved permanently.',
	    400 => 'Wrong structure of the array of transmitted data, or invalid identifiers of custom fields.',
	    401 => 'Not Authorized. There is no account information on the server. You need to make a request to another server on the transmitted IP.',
	    403 => 'The account is blocked, for repeatedly exceeding the number of requests per second.',
	    404 => 'Not found.',
	    500 => 'Internal server error.',
	    502 => 'Bad gateway.',
	    503 => 'Service unavailable.'
	];

	//if ($code < 200 || $code > 204) die( "Error $code. " . (isset($errors[$code]) ? $errors[$code] : 'Undefined error') );
	$response = json_decode($out, true);
	curl_close($curl);

	return $response;
}

//558541

/*$method = "/api/v4/leads/29728037";

$get = array(
	"with" => "catalog_elements,contacts,source_id",	
);

$headers = [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $access_token,
];

$link = "https://$subdomain.amocrm.ru".$method."?".http_build_query($get);

$curl = curl_init();
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_USERAGENT, 'amoCRM-API-client/1.0');
curl_setopt($curl, CURLOPT_URL, $link);
curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($curl, CURLOPT_HEADER, false);
$out = curl_exec($curl);
$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
$code = (int) $code;
$errors = [
    301 => 'Moved permanently.',
    400 => 'Wrong structure of the array of transmitted data, or invalid identifiers of custom fields.',
    401 => 'Not Authorized. There is no account information on the server. You need to make a request to another server on the transmitted IP.',
    403 => 'The account is blocked, for repeatedly exceeding the number of requests per second.',
    404 => 'Not found.',
    500 => 'Internal server error.',
    502 => 'Bad gateway.',
    503 => 'Service unavailable.'
];

if ($code < 200 || $code > 204) die( "Error $code. " . (isset($errors[$code]) ? $errors[$code] : 'Undefined error') );
$response = json_decode($out, true);
curl_close($curl);	
 
echo '<pre>';
print_r($response);
echo '</pre>';
echo $code;*/