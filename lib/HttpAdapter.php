<?php
//namespace Retargeting\Lib;

class HttpAdapter {
	
	protected $http = null;

	protected $timeout = 30;

	protected $config = array();

	protected $tokens = null;

	public function __construct($config, $tokens = null) {

		$this->config = $config;

		$this->tokens = $tokens;
		
		return true;
	}

	public function getAccessToken($data) {

		$url = 'https://login.bigcommerce.com/oauth2/token';

		$fields = array(
			'client_id' => $this->config['clientId'],
			'client_secret' => $this->config['clientSecret'],
			'code' => $data['code'],
			'scope' => $data['scope'],
			'grant_type' => 'authorization_code',
			'redirect_uri' => $this->config['callbackUrl'],
			'context' => $data['context']
		);
		
		$this->tokens = $this->sendRequest($url, $fields);

		return $this->tokens;
	}

	public function getShopDetails() {

		if ($this->tokens == null) return false;

		$url = 'https://api.bigcommerce.com/'.$this->tokens->context.'/v2/store';
		
		return $this->sendXAuthRequest($url);
	}

	public function getCustomer($email) {

		if ($this->tokens == null) return false;

		$url = 'https://api.bigcommerce.com/'.$this->tokens->context.'/v2/customers?email='.$email;
		
		return $this->sendXAuthRequest($url);
	}

	public function getProduct($id) {

		if ($this->tokens == null) return false;

		$url = 'https://api.bigcommerce.com/'.$this->tokens->context.'/v2/products/'.$id;
		
		return $this->sendXAuthRequest($url);
	}

	public function getProducts() {

		if ($this->tokens == null) return false;

		$url = 'https://api.bigcommerce.com/'.$this->tokens->context.'/v2/products';

		return $this->sendXAuthRequest($url);
	}

	public function getCategory($name) {

		if ($this->tokens == null) return false;

		$url = 'https://api.bigcommerce.com/'.$this->tokens->context.'/v2/categories?name='.$name;
		
		return $this->sendXAuthRequest($url);
	}

	public function getCategoryById($id) {

		if ($this->tokens == null) return false;

		$url = 'https://api.bigcommerce.com/'.$this->tokens->context.'/v2/categories/'.$id;
		
		return $this->sendXAuthRequest($url);
	}

	public function getBrand($name) {

		if ($this->tokens == null) return false;

		$url = 'https://api.bigcommerce.com/'.$this->tokens->context.'/v2/brands';

		return $this->sendXAuthRequest($url);
	}

	public function getBrandById($id) {

		if ($this->tokens == null) return false;

		$url = 'https://api.bigcommerce.com/'.$this->tokens->context.'/v2/brands/'.$id;

		return $this->sendXAuthRequest($url);
	}

	public function getOrder($id) {

		if ($this->tokens == null) return false;

		$url = 'https://api.bigcommerce.com/'.$this->tokens->context.'/v2/orders/'.$id;

		return $this->sendXAuthRequest($url);
	}

	public function getOrderProducts($id) {

		if ($this->tokens == null) return false;

		$url = 'https://api.bigcommerce.com/'.$this->tokens->context.'/v2/orders/'.$id.'/products';

		return $this->sendXAuthRequest($url);
	}

	public function getOrderCoupons($id) {

		if ($this->tokens == null) return false;

		$url = 'https://api.bigcommerce.com/'.$this->tokens->context.'/v2/orders/'.$id.'/coupons';

		return $this->sendXAuthRequest($url);
	}

	public function getCoupon($code) {

		if ($this->tokens == null) return false;

		$url = 'https://api.bigcommerce.com/'.$this->tokens->context.'/v2/coupons?code='.$code;

		return $this->sendXAuthRequest($url);
	}

	public function postCoupon($code, $type, $value) {

		if ($this->tokens == null) return false;

		$url = 'https://api.bigcommerce.com/'.$this->tokens->context.'/v2/coupons?';

		if ($type == 0) $type = 'per_total_discount';
		if ($type == 1) $type = 'percentage_discount';
		if ($type == 2) $type = 'free_shipping';

		$fields = array(
			"name" => "Ret-".$code,
			"type" => $type,
			"code" => $code,
			"enabled" => true,
			"amount" => $value,
			"applies_to" => array(
				"entity" => "categories",
				"ids" => array( 0 )
			)
		);

		$fields_string = json_encode($fields);

		return $this->sendXAuthRequestPost($url, $fields_string);
	}

	

	public function generateCoupons($params) {

		$newCoupons = array();

		for ($idx = 0; $idx < $params['count']; $idx ++) {

			$code = $this->newDiscountCode(14);

			$coupons = $this->getCoupon($code);

			while ($coupons != null && gettype($coupons) == "array" && count($coupons) > 0) {

				$code = $this->newDiscountCode(8);

				$coupons = $this->getCoupon($code);
			}

			if ($this->postCoupon($code, $params['type'], $params['value'])) {

				$newCoupons[] = $code;
			}
		}

		return json_encode($newCoupons);
	}

	private function sendRequest($url, $data) {
		
		$res = false;

		$fields = $data;
		$fields_string = '';

		foreach($fields as $key => $value) { 
			$fields_string .= $key.'='.$value.'&'; 
		}
		rtrim($fields_string, '&');

		try {
			
			$ch = curl_init();

			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
			curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
			curl_setopt($ch, CURLOPT_POST, count($fields));
			curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			
			$res = curl_exec($ch);
			
			if (false === $res)
				throw new Exception(curl_error($ch), curl_errno($ch));

			curl_close($ch);
		} catch (Exception $e) {

			trigger_error(sprintf('Curl failed with error #%d: %s', $e->getCode(), $e->getMessage()), E_USER_ERROR);
		}
			
		return json_decode($res);

    }

	private function sendXAuthRequest($url) {
		
		if (empty($this->tokens->access_token)) return false;

		try {
			
			$ch = curl_init();
			
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				'X-Auth-Client: '.$this->config['clientId'],
				'X-Auth-Token: '.$this->tokens->access_token,
				'Accept: application/json',
				'Content-Type: application/json'
				));
			curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			
			$res = curl_exec($ch);
			
			if (false === $res)
				throw new Exception(curl_error($ch), curl_errno($ch));

			curl_close($ch);

		} catch (Exception $e) {

			trigger_error(sprintf('Curl failed with error #%d: %s', $e->getCode(), $e->getMessage()), E_USER_ERROR);
		}

		//var_dump($this->tokens);

		return json_decode($res);

	}

	private function sendXAuthRequestPost($url, $fields_string) {
		
		if (empty($this->tokens->access_token)) return false;

		try {
			
			$ch = curl_init();
			
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				'X-Auth-Client: '.$this->config['clientId'],
				'X-Auth-Token: '.$this->tokens->access_token,
				'Accept: application/json',
				'Content-Type: application/json'
				));
			curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
			curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			
			$res = curl_exec($ch);
			
			if (false === $res)
				throw new Exception(curl_error($ch), curl_errno($ch));

			curl_close($ch);

		} catch (Exception $e) {

			trigger_error(sprintf('Curl failed with error #%d: %s', $e->getCode(), $e->getMessage()), E_USER_ERROR);
		}

 		// var_dump(json_decode($res));
		return json_decode($res);

	}

	private function newDiscountCode($length) {

		return strtoupper(substr(str_shuffle(MD5(microtime())), 0, $length));
	}

}