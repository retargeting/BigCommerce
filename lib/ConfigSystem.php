<?php
//namespace Retargeting\Lib;

require 'import/hash_equals.php';

require 'DataBaseConnection.php';
require 'HttpAdapter.php';

class App {
	
	protected $config = array();

	protected $db = false;

	protected $http = false;

	public $validRequest = false;

	public $shop = '';

	public $shopKey = '';

	public $status = false;

	public $domainApiKey = '';

	public $discountsApiKey = '';

	public $helpPages = '';

	public $querySelectors = array();

	protected $params = false;
	
	public function __construct($config) {

		$this->config = $config;

		$this->params = $_GET;

		$this->validRequest = $this->verifySender();

		if ($this->validRequest) {

			$this->init();

			$this->initView();
		}

        return true;
    }

	public function verifySender() {

		if (!$this->params) return false;

		list($encodedData, $encodedSignature) = explode('.', $this->params['signed_payload'], 2); 

		$signature = base64_decode($encodedSignature);
		$jsonStr = base64_decode($encodedData);
		$data = json_decode($jsonStr, true);

		$expectedSignature = hash_hmac('sha256', $jsonStr, $this->config['clientSecret'], $raw = false);
		
		if (hash_equals($expectedSignature, $signature)) {
			
			return $data;
		}
		
		return false;
    }

	private function init() {

		// DB
		$this->db = new DBConn($this->config['db']['host'], $this->config['db']['user'], $this->config['db']['pass'], $this->config['db']['db']);

		// HTTP
		$this->http = new HttpAdapter($this->config);

		return $this->db;	
	}

	private function initView() {

		// identify the store that the request pertains to & get shop Id
		$shopId = $this->db->getShopId($this->validRequest['user']['id'], $this->validRequest['user']['email'], $this->validRequest['store_hash']);

		if (!$shopId) return false;

		// get shop View
		$shopConfig = $this->db->getShopConfig($shopId);

		$this->status = $shopConfig['status'];
		$this->init = $shopConfig['init'];
		$this->domainApiKey = $shopConfig['domain_api_key'];
		$this->discountsApiKey = $shopConfig['discounts_api_key'];
		$this->querySelectors['addToCart'] = $shopConfig['qs_add_to_cart'];
		$this->helpPages = $shopConfig['help_pages'];

		$this->shopId = $shopId;
		$this->shopEmail = $this->validRequest['user']['email'];
		$this->shopContext = $this->validRequest['store_hash'];

		return true;
	}

}