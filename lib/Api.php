<?php
//namespace Retargeting\Lib;

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

		$this->init();

		$this->validRequest = $this->verifySender();

        return true;
    }

	public function verifySender() {

		if ($this->shopId && !empty($this->params['method'])) {

			return true;
		}

		return false;
    }

	private function init() {

		// Request Params
		$this->params = $_GET;
		$this->params['params'] = json_decode(urldecode($this->params['params']));

		// DB
		$this->db = new DBConn($this->config['db']['host'], $this->config['db']['user'], $this->config['db']['pass'], $this->config['db']['db']);

		// Shop ID
		// $this->shopId = $this->db->getShopIdByUrl($this->params['shop']);
		$this->shopId = $this->params['shop'];

		// HTTP
		$tokens = $this->db->getShopTokens($this->shopId);

		$this->http = new HttpAdapter($this->config, $tokens);

		return $this->db;	
	}

	public function dispatch() {

		if ($this->params['method'] == "embedd") {

			return $this->_embedd();
		} else 
		if ($this->params['method'] == "setEmail") {

			return $this->_setEmail();
		} else
		if ($this->params['method'] == "sendCategory") {

			return $this->_sendCategory();
		} else
		if ($this->params['method'] == "sendBrand") {

			return $this->_sendBrand();
		} else
		if ($this->params['method'] == "sendProduct") {

			return $this->_sendProduct();
		} else
		if ($this->params['method'] == "mouseOverPrice") {

			return $this->_mouseOverPrice();
		} else
		if ($this->params['method'] == "visitHelpPages") {

			return $this->_visitHelpPages();
		} else
		if ($this->params['method'] == "saveOrder") {

			return $this->_saveOrder();
		}

		return '/*'.json_encode(array("Error" => "Invalid Method!")).'*/';
	}

	protected function _embedd() {

		$shopData = $this->db->getShopConfig($this->shopId);

		if ($shopData['status']) {

			if (!empty($shopData['domain_api_key'])) {
				return '
					(function(){
					var ra_key = "'.$shopData['domain_api_key'].'";
					var ra = document.createElement("script"); ra.type ="text/javascript"; ra.async = true; ra.src = ("https:" ==
					document.location.protocol ? "https://" : "http://") + "retargeting-data.eu/rajs/" + ra_key + ".js";
					var s = document.getElementsByTagName("script")[0]; s.parentNode.insertBefore(ra,s);})();
				';
			} else {
				return '
					(function(){
					var ra = document.createElement("script"); ra.type ="text/javascript"; ra.async = true; ra.src = ("https:" ==
					document.location.protocol ? "https://" : "http://") + "retargeting-data.eu/" +
					document.location.hostname.replace("www.","") + "/ra.js"; var s =
					document.getElementsByTagName("script")[0]; s.parentNode.insertBefore(ra,s);})();
				';
			}
		}

		return '/*'.json_encode(array("Info" => "Retargeting App is disabled!")).'*/';
	}

	protected function _setEmail() {

		$shopData = $this->db->getShopConfig($this->shopId);

		if (empty($this->params['params']->id)) return '/*'.json_encode(array("Info" => "Invalid params!")).'*/';

		if ($shopData['status']) {

			$entityEmail = $this->params['params']->id;

			$customers = $this->http->getCustomer($entityEmail);

			if ($customers != null && count($customers) > 0 && property_exists($customers[0], 'email')) {

				$name = [$customers[0]->first_name, $customers[0]->last_name];

				return '_ra.setEmail({
						"email": "'.$customers[0]->email.'",
						"name": "'.implode(' ', $name).'",
						"phone": "'.$customers[0]->phone.'"
					});
				';	
			}

			return '_ra.setEmail({
					"email": "'.$entityEmail.'"
				});
			';
		}

		return '/*'.json_encode(array("Info" => "Retargeting App is disabled!")).'*/';
	}

	protected function _sendCategory() {

		$shopData = $this->db->getShopConfig($this->shopId);

		if (empty($this->params['params']->id)) return '/*'.json_encode(array("Info" => "Invalid params!")).'*/';

		if ($shopData['status']) {

			$entityName = $this->params['params']->id;

			$category = $this->http->getCategory($entityName);

			if ($category != null && count($category) > 0 && property_exists($category[0], 'name')) {

				return 'var _ra = _ra || {};
					_ra.sendCategoryInfo = {
						"id": "'.$category[0]->id.'",
						"name" : "'.$category[0]->name.'",
						"parent": false,
						"category_breadcrumb": []
					}

					if (_ra.ready !== undefined) {
						_ra.sendCategory(_ra.sendCategoryInfo);
					};
				';
			}
		}

		return '/*'.json_encode(array("Info" => "Retargeting App is disabled!")).'*/';
	}

	protected function _sendBrand() {

		$shopData = $this->db->getShopConfig($this->shopId);

		if (empty($this->params['params']->id)) return '/*'.json_encode(array("Info" => "Invalid params!")).'*/';

		if ($shopData['status']) {

			$entityName = $this->params['params']->id;

			$brands = $this->http->getBrand($entityName);

			if ($brands != null && count($brands) > 0 && property_exists($brands[0], 'name')) {

				foreach ($brands as $brand) {

					if ($brand->name == htmlspecialchars_decode($entityName))
						return 'var _ra = _ra || {};
							_ra.sendBrandInfo = {
								"id": "'.$brand->id.'",
								"name": "'.$brand->name.'"
							};

							if (_ra.ready !== undefined) {
								_ra.sendBrand(_ra.sendBrandInfo);
							}
						';
				}
				
			}

			return '/*'.json_encode(array("Sorry" => "Something went wrong!")).'*/';
		}

		return '/*'.json_encode(array("Info" => "Retargeting App is disabled!")).'*/';
	}

	protected function _sendProduct() {

		$shopData = $this->db->getShopConfig($this->shopId);

		if (empty($this->params['params']->id)) return '/*'.json_encode(array("Info" => "Invalid params!")).'*/';

		if ($shopData['status']) {

			$entityId = $this->params['params']->id;

			$product = $this->http->getProduct($entityId);

			if ($product != null && gettype($product) == "object" && property_exists($product, 'name')) {
			
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
			
				return 'var _ra_ProductId = "'.$product->id.'";
					var _ra = _ra || {};
					_ra.sendProductInfo = {
						"id": "'.$product->id.'",
						"name": "'.$product->name.'",
						"url": window.location.href,
						"img": "'.$product->primary_image->standard_url.'",
						"price": "'.$product->price.'",
						"promo": "'.($product->sale_price != 0 ? $product->sale_price : 0 ).'",
						"stock": '.($product->availability == 'available' ? 1 : 0).',
						"brand": '.$brand.',
						"category": '.$category.',
						"category_breadcrumb": []
					};
					
					if (_ra.ready !== undefined) {
						_ra.sendProduct(_ra.sendProductInfo);
					}
				';

			}
		}

		return '/*'.json_encode(array("Info" => "Retargeting App is disabled!")).'*/';
	}

	protected function _mouseOverPrice() {

		$shopData = $this->db->getShopConfig($this->shopId);

		if (empty($this->params['params']->id)) return '/*'.json_encode(array("Info" => "Invalid params!")).'*/';

		if ($shopData['status']) {

			$entityId = $this->params['params']->id;

			$product = $this->http->getProduct($entityId);

			if ($product != null && property_exists($product, 'name')) {

				return 'if (typeof _ra.mouseOverPrice !== "undefined") {
					_ra.mouseOverPrice("'.$product->id.'", {
						"price": "'.$product->price.'",
						"promo": "'.($product->sale_price != 0 ? $product->sale_price : 0 ).'",
					});
				}
				';
			}

			return '_ra.mouseOverPrice("'.$product->id.'");
				';
		}

		return '/*'.json_encode(array("Info" => "Retargeting App is disabled!")).'*/';
	}

	protected function _visitHelpPages() {

		$shopData = $this->db->getShopConfig($this->shopId);

		if (empty($this->params['params']->id)) return '/*'.json_encode(array("Info" => "Invalid params!")).'*/';

		if ($shopData['status']) {

			$entityId = $this->params['params']->id;

			$pageHandles = explode(',', str_replace(' ', '', $shopData['help_pages']));

			foreach ($pageHandles as $pageHandle) {
				if ($entityId == $pageHandle || $entityId == '/'.$pageHandle || $entityId == $pageHandle.'/' || $entityId == '/'.$pageHandle.'/' ) {
					return 'var _ra = _ra || {};
						_ra.visitHelpPageInfo = {
							"visit" : true
						}

						if (_ra.ready !== undefined) {
							_ra.visitHelpPage();
						}
					';
				}
			}
		} else {

			return '/*'.json_encode(array("Info" => "Retargeting App is disabled!")).'*/';
		}
	}

	protected function _saveOrder() {

		$shopData = $this->db->getShopConfig($this->shopId);

		if (empty($this->params['params']->id)) return '/*'.json_encode(array("Info" => "Invalid params!")).'*/';

		if ($shopData['status']) {

			$entityId = $this->params['params']->id;

			$order = $this->http->getOrder($entityId);

			if ($order != null && property_exists($order, 'id')) {

				$couponsCode = '';

				$coupons = $this->http->getOrderCoupons($entityId);

				if ($coupons != null && count($coupons) > 0 && property_exists($coupons[0], 'code')) {

					$couponsCode = array();
					foreach ($coupons as $coupon) {

						$couponsCode[] = $coupon->code;
					}

					$couponsCode = implode(', ', $couponsCode);
				}

				$productsArr = array();

				$products = $this->http->getOrderProducts($entityId);

				if ($products != null && count($products) > 0 && property_exists($products[0], 'name')) {

					$productsArr = array();
					foreach ($products as $product) {

						$variation = array();

						foreach ($product->product_options as $option) {
							$variation[] = $option->display_value;
						}

						$productsArr[] = '{
							"id": "'.$product->product_id.'",
							"quantity": '.$product->quantity.',
							"price": "'.$product->price_inc_tax.'",
							"variation_code": "'.implode('-', $variation).'"
						}';
					}

					$productsArr = implode(', ', $productsArr);
				}				

				return 'var _ra = _ra || {};
					_ra.saveOrderInfo = {
						"order_no": "'.$order->id.'",
						"lastname": "'.$order->billing_address->last_name.'",
						"firstname": "'.$order->billing_address->first_name.'",
						"email": "'.$order->billing_address->email.'",
						"phone": "'.$order->billing_address->phone.'",
						"state": "'.$order->billing_address->state.'",
						"city": "'.$order->billing_address->city.'",
						"address": "'.$order->billing_address->street_1.' '.$order->billing_address->street_2.'",
						"discount_code": "'.$couponsCode.'",
						"discount": "'.$order->coupon_discount.'",
						"shipping": "'.$order->shipping_cost_inc_tax.'",
						"total": "'.$order->total_inc_tax.'"
					};
					_ra.saveOrderProducts = [
						'.$productsArr.'
					];
					
					if( _ra.ready !== undefined ){
						_ra.saveOrder(_ra.saveOrderInfo, _ra.saveOrderProducts);
					}
				';
			}
		} else {

			return '/*'.json_encode(array("Info" => "Retargeting App is disabled!")).'*/';
		}
	}

	/*
	protected function searchCategoryTree($node) {

		$this->categoryPath[] = $node->id;
		
		$ret = false;

		foreach ($node->children as $childNode) {
			
			if ($childNode->id == $category->category_id) {
				// return $road;
			}

			$ret = $this->searchCategoryTree($childNode);
		}

		return $ret;
	}
	*/
}