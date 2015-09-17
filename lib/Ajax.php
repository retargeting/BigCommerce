<?php
//namespace Retargeting\Lib;

require 'DataBaseConnection.php';

class App {
	
	protected $config = array();

	protected $db = false;

	public $validRequest = false;

	protected $shopId = false;

	public function __construct($config) {

		$this->config = $config;

		$this->init();

		$this->validRequest = $this->verifySender();

        return true;
    }

	public function verifySender() {

		$params = $_POST;

		$shopId = $this->db->getShopId($params['shopId'], $params['shopEmail'], $params['shopContext']);

		if ($shopId) {

			$this->shopId = $shopId;

			return true;
		}

		return false;
    }

	private function init() {

		// DB
		$this->db = new DBConn($this->config['db']['host'], $this->config['db']['user'], $this->config['db']['pass'], $this->config['db']['db']);

		return $this->db;	
	}

	public function dispatch() {

		$params = $_POST;

		if (isset($params['disableInit'])) {

			return json_encode($this->db->disableInit($this->shopId));
		} else if (isset($params['domainApiKey']) && isset($params['discountsApiKey']) && isset($params['qs_add_to_cart']) && isset($params['help_pages'])) {

			return json_encode($this->db->updateShopConfig($this->shopId, (empty($params['changeStatus']) ? false : true ), $params['domainApiKey'], $params['discountsApiKey'], $params['help_pages'], $params['qs_add_to_cart']));
		}
	}

}