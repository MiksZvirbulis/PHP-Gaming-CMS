<?php

class suncore{
	protected $client_id = 26;
	protected $key;
	protected $prices = [];

	public function setPrices($prices = array()){
		$this->prices = $prices;
	}

	public function returnPrices(){
		return $this->prices;
	}

	public function request($key, $price){
		$request = @file_get_contents("http://run.baltgroup.eu/api/sms/charge/?client=" . $this->client_id . "&code=" . $key . "&price=" . $price, FALSE, NULL, 0);
		if($request === false){
			return "ABORTED";
		}else{
			return $request;
		}
	}
}