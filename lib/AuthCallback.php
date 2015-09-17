<?php
//namespace Retargeting\Lib;

require 'DataBaseConnection.php';
require 'HttpAdapter.php';

class App {
	
	protected $config = array();

	protected $params = array();

	protected $db = false;

	protected $http = false;

	public $validRequest = false;

	public function __construct($config, $params = array()) {
        
        $this->config = $config;
        $this->params = $params;

		// $this->validRequest = $this->verifySender();

		// if ($this->validRequest) {
        	
        	return $this->init();

        // }

        return false;
    }

    public function install($data = null) {
    	
    	if ($data == null) $data = $this->params;

		
		// get OAuth tokens
		$tokens = $this->http->getAccessToken($this->params);

		// get Shop's URL
		$shopDetails = $this->http->getShopDetails();

		// shop installation & store permanent token
		$shopId = $this->db->saveShop($tokens->access_token, $tokens->scope, $tokens->user->id, $tokens->user->email, $tokens->context, $shopDetails->domain);

    	return $shopId;
    }

	private function init() {

		// DB
		$this->db = new DBConn($this->config['db']['host'], $this->config['db']['user'], $this->config['db']['pass'], $this->config['db']['db']);

		// HTTP
		$this->http = new HttpAdapter($this->config);

		return $this->db;	
	}
}