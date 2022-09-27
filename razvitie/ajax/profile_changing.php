<?
require_once($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/include/prolog_before.php");

if(!CModule::IncludeModule("main")){
	echo json_encode(array(
		"error"=>"Ошибка подключения"
	));
	die();
}

global $USER;

class ProfileHandler{
	private $isErrorFound;
	private $error;
	private $result;
	private $arFilter;
	private $request;
	private $user;

	public function __construct($request, $user){
		$this->isErrorFound = false;
		$this->error = false;
		$this->result = [];
		$this->arFilter = [];
		$this->request = $request;
		$this->user = $user;
	}

	public function sendAnswer(){
		$this->handleRequest();
		if($_SERVER["REQUEST_METHOD"]=='POST'&&check_bitrix_sessid()){
			foreach($this->request as $key=>$value){
				switch($key){
					case "NAME":
						$this->setName();
						break;
					case "CITY":
						$this->setCity();
						break;
					case "EMAIL":
						$this->setEmail();
						break;
					case "PHONE":
						$this->setPhone();
						break;
					case "STREET":
						$this->setStreet();
						break;
				}
			}
			$this->updateUser();
			$this->showAnswer();
		}
		else{
			$this->error = "Ошибка запроса";
			$this->showAnswer();
		}
	}

	private function handle($data){
		return htmlspecialchars(stripslashes(trim($data)));
	}

	private function handleRequest(){
		foreach ($this->request as $key => $value) {
			$this->request[$key] = $this->handle($this->request[$key]);
		}
	}

	private function setName(){
		if(!($this->request["NAME"]=="Иия не указано"||!$this->request["NAME"])){
			$this->request["NAME"] = preg_replace('/\s+/', " ", $this->request["NAME"]);
			if(preg_match('/[^A-Za-zА-Яа-яЁё\s]/ui', $this->request["NAME"])){
				if(!$this->isErrorFound){
					$this->error = 'Для ввода имени используйте только символы русского и английского алфавитов';
					$this->isErrorFound = true;
				}
				$this->result["NAME"] = $this->user->GetFullName();
			}
			else{
				$res = explode(" ", $this->request["NAME"]);
				if(count($res)==1){
					$this->arFilter["NAME"] = $res[0];
				}
				else{
					$this->arFilter["NAME"] = $res[1];
					$this->arFilter["LAST_NAME"] = $res[0];
				}
			}
		}
		else{
			$this->result["NAME"] = $this->user->GetFullName();
		}
	}

	private function setCity(){
		if(!($this->request["CITY"]=="Город проживания не указан"||!$this->request["CITY"])){
			$this->request["CITY"] = preg_replace('/\s+/', " ", $this->request["CITY"]);
			if(preg_match('/[^-A-Za-zА-Яа-яЁё\s,]/ui', $this->request["CITY"])){
				if(!$this->isErrorFound){
					$this->error = 'Введите город и страну через запятую либо только город, используя символы русского и английского алфавитов, запятую и дефис';
					$this->isErrorFound = true;
				}
				if($this->user->GetParam('PERSONAL_CITY')){
					$this->result["CITY"] = $this->GetParam('PERSONAL_CITY');
					if($this->user->GetParam('PERSONAL_COUNTRY')){
						$this->result['CITY'] .= ", ".$this->user->GetParam('PERSONAL_COUNTRY');
					}
				}
				else{
					$this->result['CITY'] = '';
				}
			}
			else{
				$res = explode(",", $this->request["CITY"]);
				if(count($res)==1){
					$this->arFilter["PERSONAL_CITY"] = $this->handle($res[0]);
				}
				else{
					$this->arFilter["PERSONAL_CITY"] = $this->handle($res[0]);
					$this->arFilter["PERSONAL_COUNTRY"] = $this->handle($res[1]);
				}
			}

		}
		else{
			if($this->user->GetParam('PERSONAL_CITY')){
				$this->result["CITY"] = $this->GetParam('PERSONAL_CITY');
				if($this->user->GetParam('PERSONAL_COUNTRY')){
					$this->result['CITY'] .= ", ".$this->user->GetParam('PERSONAL_COUNTRY');
				}
			}
			else{
				$this->result['CITY'] = '';
			}
		}
	}

	private function setEmail(){
		if($this->request["EMAIL"]){
			if(!check_email($this->request["EMAIL"])){
				if(!$this->isErrorFound){
					$this->error = "Введите корректный e-mail";
					$this->isErrorFound	= true;
				}
				$this->result["EMAIL"] = $this->user->GetEmail();
			}
			else{
				$this->arFilter["EMAIL"] = $this->request["EMAIL"];
			}
		}
		else{
			$this->result["EMAIL"] = $this->user->GetEmail();
		}
	}

	private function setPhone(){
		$this->result["phone"] = $this->request["PHONE"];
		if($this->request["PHONE"]){
			if(!preg_match('/^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$/ui', $this->request["PHONE"])){
				if(!$this->isErrorFound){
					$this->error = "Введите корректный номер телефона";
					$this->isErrorFound	= true;
				}
				$this->result["PHONE"] = $this->user->GetParam("PERSONAL_PHONE");
			}
			else{
				$this->arFilter["PERSONAL_PHONE"] = $this->request["PHONE"];
			}
		}
		else{
			$this->result["PHONE"] = $this->user->GetParam("PERSONAL_PHONE");
		}
	}

	private function setStreet(){
		if(!($this->request["STREET"]=="Место проживания не указано"||!$this->request["STREET"])){
			if(preg_match('/[^-A-Za-zА-яа-яЁё\s,\.]/ui', $this->request["STREET"])){
				if(!$this->isErrorFound){
					$this->error = "Для ввода адреса используйте символы русского и английского алфавитов, пробел, точку и запятую";
					$this->isErrorFound = true;
				}
				$this->result["STREET"] = $this->user->GetParam("PERSONAL_STREET");
			}
			else{
				$this->arFilter["PERSONAL_STREET"] = $this->request["STREET"];
			}
		}
		else{
			$this->result["STREET"] = $this->user->GetParam("PERSONAL_STREET");
		}
	}
	
	private function updateUser(){
		$user = new Cuser;
		$user->Update($this->user->GetID(), $this->arFilter);
		foreach($this->arFilter	as $key=>$value){
			switch($key){
				case "NAME":{
					$this->result["NAME"] = $this->arFilter["NAME"];
					if(isset($this->arFilter["LAST_NAME"])){
						$this->result["NAME"] = $this->arFilter["LAST_NAME"]." ".$this->result["NAME"];
					}				
					break;
				}
				case "PERSONAL_CITY":{
					$this->result["CITY"] = $this->arFilter['PERSONAL_CITY'];
					if(isset($this->$arFilter["PERSONAL_COUNTRY"])){
						$this->result['CITY'] .= ", ".$this->arFilter['PERSONAL_COUNTRY'];
					}
					break;
				}
				case "EMAIL":
					$this->result["EMAIL"] = $this->arFilter["EMAIL"];
					break;
				case "PERSONAL_PHONE":
					$this->result["PHONE"] = $this->arFilter["PERSONAL_PHONE"];
					break;
				case "PERSONAL_STREET":
					$this->result["STREET"] = $this->arFilter["PERSONAL_STREET"];
					break;
			}
		}
	}

	private function showAnswer(){
		echo json_encode(
			array(
				"error"=>$this->error,
				"result"=>$this->result,
				"arFilter"=>$this->arFilter
			)
		);
	}
};

if($USER->isAuthorized()){
	$handler = new ProfileHandler($_REQUEST, $USER);
	//CUser::Update(1, array("NAME"=>"NO"));
	$handler->sendAnswer();
	//echo json_encode(array("res"=>$USER->IsAuthorized()));
}
else{
	echo json_encode(array(
		"error"=>"Пожалуйста, авторизуйтесь"
	));
}