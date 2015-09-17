<?php
//namespace Retargeting\Lib;

require 'import/hash_equals.php';

require 'DataBaseConnection.php';
require 'HttpAdapter.php';

class App {
	
	protected $config = array();

	protected $db = false;

	protected $shopId = false;

	protected $params =  array();

	public $validRequest = false;
	
	public function __construct($config) {

		$this->config = $config;

		$this->params = $_GET;

		$this->validRequest = $this->verifySender();

		if ($this->validRequest) {

			$this->init();
		}

        return true;
    }

	public function verifySender() {

		if (empty($this->params['key']) || !isset($this->params['type']) || !isset($this->params['value']) || empty($this->params['count'])) return false;

		// DB
		$this->db = new DBConn($this->config['db']['host'], $this->config['db']['user'], $this->config['db']['pass'], $this->config['db']['db']);

		if (!$this->params) return false;
		
		if ($this->params['id'] == false || $this->params['id'] != $this->db->getShopIdByUrl($this->params['d'])) return false;

		return true;
    }

	private function init() {

		// Shop ID
		$this->shopId = $this->params['id'];

		// HTTP
		$tokens = $this->db->getShopTokens($this->shopId);

		$this->http = new HttpAdapter($this->config, $tokens);

		return $this->db;	
	}

	public function generateCoupons() {

		$shopData = $this->db->getShopConfig($this->shopId);

		if ($shopData['status']) {

			if ($shopData['discounts_api_key'] !== '' && $shopData['discounts_api_key'] == $this->params['key']) {

				return $this->http->generateCoupons($this->params);

			} else {
				return '/*'.json_encode(array("Info" => "Invalid Discounts API Key")).'*/';
			}

			return '/*'.json_encode(array("Info" => "Something went wrong..")).'*/';
		}

		return '/*'.json_encode(array("Info" => "Retargeting App is disabled!")).'*/';
	}

}