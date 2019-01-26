<?php
class geo{
	protected $api = "https://freegeoip.net/json/%s";
	protected $properties = [];

	public function __get($key){
		if(isset($this->properties[$key])){
			return $this->properties[$key];
		}else{
			return false;
		}
	}

	public function request($ip_address){
		$data = $this->sendRequest(sprintf($this->api, $ip_address));
		$this->properties = json_decode($data, true);
	}

	protected function sendRequest($url){
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_URL, $url);
		return curl_exec($curl);
	}
}