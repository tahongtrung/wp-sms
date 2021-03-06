<?php
	class smsbartar extends WP_SMS {
		private $wsdl_link = "http://sms.sms-bartar.com/webservice/?WSDL";
		public $tariff = "http://www.sms-bartar.com/%D9%BE%D9%86%D9%84-%D8%A7%D8%B3-%D8%A7%D9%85-%D8%A7%D8%B3-%D8%AB%D8%A7%D8%A8%D8%AA";
		public $unitrial = true;
		public $unit;
		public $flash = "disable";
		public $isflash = false;

		public function __construct() {
			parent::__construct();
			$this->validateNumber = "09xxxxxxxx";
			
			ini_set("soap.wsdl_cache_enabled", "0");
		}

		public function SendSMS() {
			$options = array('login' => $this->username, 'password' => $this->password);
			$client = new SoapClient($this->wsdl_link, $options);

			$result = $client->sendToMany($this->to, $this->msg, $this->from);
			
			if($result) {
				$this->InsertToDB($this->from, $this->msg, $this->to);
				$this->Hook('wp_sms_send', $result);
			}
			
			return $result;
		}

		public function GetCredit() {
			$options = array('login' => $this->username, 'password' => $this->password);
			$client = new SoapClient($this->wsdl_link, $options);

			try
			{
				$credit = $client->accountInfo();
				return $credit->remaining;
			}

			catch (SoapFault $sf)
			{
				return $sf->faultcode."\n";
				return $sf->faultstring."\n";
			}
		}
	}
?>