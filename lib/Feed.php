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

	public function initView() {

		$shopData = $this->db->getShopConfig($this->shopId);

		if ($shopData['status']) {

			$res = '';

			$products = $this->http->getProducts();
			
			if ($products != null && count($products) > 0 && property_exists($products[0], 'name')) {

				$res = '<?xml version="1.0" encoding="UTF-8"?>';
				$res .= '<products>';

				foreach ($products as $product) {

					/*
					$brand = $this->http->getBrandById($product->brand_id);

					if ($brand != null && gettype($brand) == "object" && property_exists($brand, 'name')) {
						$brand = '{
							"id": "'.$brand->id.'",
							"name": "'.$brand->name.'"
						}';
					} else {
						$brand = 'false';
					}
					
					$category = (count($product->categories) > 0 ? $this->http->getCategoryById($product->categories[0]) : 'false');

					if ($category != 'false' && $category != null && gettype($category) == "object" && property_exists($category, 'name')) {
						$category = '{
							"id": "'.$category->id.'",
							"name" : "'.$category->name.'",
							"parent": false
						}';
					}
					*/

					$res .= '<product>
						<id>'.$product->id.'</id>
						<stock>'.($product->availability == 'available' ? 1 : 0).'</stock>
						<price>'.$product->price.'</price>
						<promo>'.($product->sale_price != 0 ? $product->sale_price : 0 ).'</promo>
						<url>http://'.$this->params['d'].$product->custom_url.'</url>
						<image>'.$product->primary_image->standard_url.'</image>
					</product>';
				}

				$res .= '</products>';
			}

			return $res;
		}

		return '/*'.json_encode(array("Info" => "Retargeting App is disabled!")).'*/';
	}

}